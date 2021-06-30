<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DtoModel\Users\RegistrationUserDtoModel;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationsException;
use App\Manager\UserManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route(path="", methods={"POST"}, name="app_users_registration")
     * @OA\Post(
     *     tags={"Users"},
     *     summary="User registration",
     *     description="Registration",
     *     @OA\RequestBody(
     *         description="User`s info",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="firstName", description="First name", type="string"),
     *                 @OA\Property(property="lastName", description="Last name", type="string"),
     *                 @OA\Property(property="patronymic", description="Patronymic", type="string"),
     *                 @OA\Property(property="email", description="Email", type="string"),
     *                 @OA\Property(property="phone", description="Phone", type="string"),
     *                 @OA\Property(property="password", description="Password", type="string"),
     *                 @OA\Property(property="confirmPassword", description="Confirm password", type="string")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response="201",
     *         description="Created"
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request"
     *     )
     * )
     * @param UserManager $manager
     * @param ValidatorInterface $validator
     * @param RegistrationUserDtoModel $dtoModel
     * @return JsonResponse
     */
    public function registration(
        UserManager $manager,
        ValidatorInterface $validator,
        RegistrationUserDtoModel $dtoModel
    ): JsonResponse {
        try {
            $errors = $validator->validate($dtoModel);
            if ($errors->count() > 0) {
                throw new ConstraintsValidationsException($errors, Response::HTTP_BAD_REQUEST);
            }
            $client = $manager->registration(
                $dtoModel->firstName,
                $dtoModel->lastName,
                $dtoModel->email,
                $dtoModel->phone,
                $dtoModel->password,
                $dtoModel->confirmPassword,
                $dtoModel->patronymic
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($client, Response::HTTP_CREATED);
    }
}