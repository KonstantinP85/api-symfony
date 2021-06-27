<?php

declare(strict_types=1);

namespace App\Controller;

use App\DtoModel\Security\ChangePasswordDtoModel;
use App\Entity\User;
use App\Exception\AppException;
use App\Exception\ApiException;
use App\Exception\ConstraintsValidationsException;
use App\Manager\SecurityManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="api/security")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route(path="/login", methods={"POST"}, name="app_security_login")
     * @OA\Post(
     *     tags={"Security"},
     *     summary="Authorization",
     *     description="User authorization",
     *     @OA\RequestBody(
     *         description="User`s info",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="username", description="User`s email", type="string"),
     *                 @OA\Property(property="password", description="User`s password", type="string"),
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request"
     *     )
     * )
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'invalid credentials');
        }
        return $this->json(['username' => $user->getUsername(), 'roles' => $user->getRoles()]);
    }

    /**
     * @Route(path="/logout", methods={"GET"}, name="app_security_logout")
     * @OA\Get(
     *     tags={"Security"},
     *     summary="Logout",
     *     description="User logout",
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     )
     * )
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new \Exception('');
    }

    /**
     * @Route(path="/confirm-email/{token}", methods={"GET"}, name="app_security_confirm_email")
     * @OA\Get(
     *     tags={"Security"},
     *     summary="Confirm registration",
     *     description="User confirm registration",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         description="confirm token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Token not found"
     *     )
     * )
     * @param string $token
     * @param SecurityManager $manager
     * @return Response
     * @throws AppException
     */
    public function confirmEmail(string $token, SecurityManager $manager): Response
    {
        $manager->confirmEmail($token);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/change-password", methods={"POST"}, name="app_security_change_password")
     * @OA\Post(
     *     tags={"Security"},
     *     summary="Change user password",
     *     description="Change password",
     *     @OA\RequestBody(
     *         description="New and old passwords",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="oldPassword", description="Old password", type="string"),
     *                 @OA\Property(property="newPassword", description="New password", type="string")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized Error"
     *     )
     * )
     * @param SecurityManager $manager
     * @param ChangePasswordDtoModel $model
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function changePassword(
        SecurityManager $manager,
        ChangePasswordDtoModel $model,
        ValidatorInterface $validator
    ): JsonResponse {
        try {
            $errors = $validator->validate($model);
            if ($errors->count() > 0) {
                throw new ConstraintsValidationsException($errors, Response::HTTP_BAD_REQUEST);
            }
            $manager->changePassword($model->oldPassword, $model->newPassword);
        } catch (AppException $e) {
            throw new ApiException($e);
        }
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}