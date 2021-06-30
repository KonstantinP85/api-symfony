<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\UserManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route(path="/{id}", methods={"GET"}, name="app_admin_users_get_details")
     * @OA\Get(
     *     tags={"Admin"},
     *     summary="User details",
     *     description="Show user details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id user",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="User not found"
     *     )
     * )
     * @param string $id
     * @param UserManager $manager
     * @return JsonResponse
     */
    public function details(string $id, UserManager $manager): JsonResponse
    {
        try {
            $client = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($client, Response::HTTP_OK);
    }
}