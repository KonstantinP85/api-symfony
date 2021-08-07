<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\HotelManager;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/hotels")
 */
class HotelController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app_user_hotels_get_list")
     * @OA\Get(
     *     tags={"Hotels"},
     *     summary="Hotels list",
     *     description="Get list of hotels",
     *     @OA\Parameter(
     *         name="hotelName",
     *         in="query",
     *         description="Name",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="Address",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="costOneDay",
     *         in="query",
     *         description="Cost one day",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example="17"),
     *             @OA\Property(property="hotels", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request"
     *     )
     * )
     * @param Request $request
     * @param HotelManager $manager
     * @return JsonResponse
     * @throws AppException
     */
    public function getList(Request $request, HotelManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $hotelsList = $manager->search($filters);
        } catch (ApiException $e) {
            throw new ApiException($e);
        }

        return $this->json($hotelsList, Response::HTTP_OK);
    }
}