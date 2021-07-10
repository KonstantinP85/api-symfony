<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\ApiException;
use App\Manager\HotelManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HotelController extends AbstractController
{
    /**
     * @Route(path="", methods={"GET"}, name="app_user_hotels_get_list")
     *
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