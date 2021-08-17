<?php

declare(strict_types=1);

namespace App\Manager;

use App\DtoModel\HotelsSearchResultModel;
use App\Entity\Hotel;
use App\Exception\AppException;
use App\Repository\HotelRepository;
use App\Traits\EntityManagerTrait;
use Doctrine\ORM\UnexpectedResultException;
use Symfony\Component\HttpFoundation\Response;

class HotelManager
{
    use EntityManagerTrait;

    /**
     * @var HotelRepository
     */
    private HotelRepository $hotelRepository;

    /**
     * HotelManager constructor.
     * @param HotelRepository $hotelRepository
     */
    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }
    /**
     * @param string $name
     * @param string $description
     * @param int $costOneDay
     * @param string $address
     * @return Hotel
     */
    public function create(string $name, string $description, int $costOneDay, string $address): Hotel
    {
        $hotel = new Hotel($name, $description, $costOneDay, $address);
        $this->entityManager->persist($hotel);
        $this->entityManager->flush();

        return $hotel;
    }

    /**
     * @param string $hotelId
     * @return Hotel
     * @throws AppException
     */
    public function get(string $hotelId): Hotel
    {
        $hotel = $this->hotelRepository->find($hotelId);
        if (!$hotel instanceof Hotel) {
            throw new AppException('Hotel not found', Response::HTTP_NOT_FOUND);
        }

        return $hotel;
    }

    /**
     * @param array<string, string> $filters
     * @return HotelsSearchResultModel
     * @throws AppException
     */
    public function search(array $filters): HotelsSearchResultModel
    {
        try {
            $hotels = $this->hotelRepository->searchHotelsList($filters);
            $total = count($hotels);

            return new HotelsSearchResultModel($total, $hotels);
        } catch (UnexpectedResultException $e) {
            throw new AppException($e->getMessage(), Response::HTTP_BAD_REQUEST, $e);
        }
    }
}