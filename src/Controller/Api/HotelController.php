<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Exception\AppException;
use App\Manager\HotelManager;
use App\Serializer\Normalizer\HotelNormalizer;
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
     */
    public function listAction(Request $request, HotelManager $manager): JsonResponse
    {
        try {
            $filters = $request->query->all();
            $hotelsList = $manager->search($filters);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($hotelsList, Response::HTTP_OK, [], [HotelNormalizer::CONTEXT_TYPE_KEY => HotelNormalizer::TYPE_LIST]);
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, name="app_user_hotels_get_details")
     * @OA\Get(
     *     tags={"Hotels"},
     *     summary="Hotel details",
     *     description="Show hotel details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id hotel",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="id"),
     *             @OA\Property(property="name", type="string", example="Resort"),
     *             @OA\Property(property="description", type="string", example="Short description"),
     *             @OA\Property(property="costOneDay", type="string", example="1200"),
     *             @OA\Property(property="address", type="string", example="Street")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Hotel not found"
     *     )
     * )
     * @param string $id
     * @param HotelManager $manager
     * @return JsonResponse
     */
    public function detailsAction(string $id, HotelManager $manager): JsonResponse
    {
        try {
            $hotel = $manager->get($id);
        } catch (AppException $e) {
            throw new ApiException($e);
        }

        return $this->json($hotel, Response::HTTP_OK);
    }
}