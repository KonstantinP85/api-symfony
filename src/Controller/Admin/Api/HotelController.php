<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\DtoModel\Hotel\CreateHotelDtoModel;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationsException;
use App\Manager\HotelManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/hotels")
 */
class HotelController extends  AbstractController
{
    /**
     * @Route(path="", methods={"POST"}, name="app_admin_hotel_create")
     * @OA\Post(
     *     tags={"Admin hotels"},
     *     summary="Hotel create",
     *     description="Create",
     *     @OA\RequestBody(
     *         description="Hotel`s info",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="name", description="Name", type="string"),
     *                 @OA\Property(property="description", description="Description", type="string"),
     *                 @OA\Property(property="costOneDay", description="Cost one day", type="integer"),
     *                 @OA\Property(property="address", description="Address", type="string")
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
     * @param HotelManager $manager
     * @param ValidatorInterface $validator
     * @param CreateHotelDtoModel $dtoModel
     * @return JsonResponse
     */
    public function createAction(
        HotelManager $manager,
        ValidatorInterface $validator,
        CreateHotelDtoModel $dtoModel
    ): JsonResponse {
        try {
            $errors = $validator->validate($dtoModel);
            if ($errors->count() > 0) {
                throw new ConstraintsValidationsException($errors, Response::HTTP_BAD_REQUEST);
            }
            $hotel = $manager->create(
                $dtoModel->name,
                $dtoModel->description,
                $dtoModel->costOneDay,
                $dtoModel->address
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($hotel, Response::HTTP_CREATED);
    }
}