<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DtoModel\Booking\CreateBookingDtoModel;
use App\Exception\ApiException;
use App\Exception\AppException;
use App\Exception\ConstraintsValidationsException;
use App\Manager\BookingManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/booking")
 */
class BookingController extends  AbstractController
{
    /**
     * @Route(path="", methods={"POST"}, name="app_user_booking_create")
     * @OA\Post(
     *     tags={"User bookings"},
     *     summary="Booking create",
     *     description="Create",
     *     @OA\RequestBody(
     *         description="Booking`s info",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="hotelId", description="Hotel", type="string"),
     *                 @OA\Property(property="arrivalTime", description="Arrival time", type="string"),
     *                 @OA\Property(property="duration", description="Duration", type="integer")
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
     * @param BookingManager $manager
     * @param ValidatorInterface $validator
     * @param CreateBookingDtoModel $dtoModel
     * @return JsonResponse
     */
    public function createAction(
        BookingManager $manager,
        ValidatorInterface $validator,
        CreateBookingDtoModel $dtoModel
    ): JsonResponse {
        try {
            $errors = $validator->validate($dtoModel);
            if ($errors->count() > 0) {
                throw new ConstraintsValidationsException($errors, Response::HTTP_BAD_REQUEST);
            }
            $hotel = $manager->create(
                $dtoModel->hotelId,
                $dtoModel->arrivalTime,
                $dtoModel->duration,
            );
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($hotel, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app_user_booking_get_details")
     * @OA\Get(
     *     tags={"User bookings"},
     *     summary="Booking details",
     *     description="Show booking details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id booking",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="hotel", type="object"),
     *             @OA\Property(property="arrivalTime", type="string", example="12.09.2021 14:00:00"),
     *             @OA\Property(property="duration", type="string", example="11"),
     *             @OA\Property(property="status", type="string", example="new")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Booking is not found"
     *     )
     * )
     * @param string $id
     * @param BookingManager $manager
     * @return JsonResponse
     */
    public function detailsAction(string $id, BookingManager $manager): JsonResponse
    {
        try {
            $booking = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($booking, Response::HTTP_OK);
    }
}