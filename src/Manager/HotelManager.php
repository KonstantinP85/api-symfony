<?php

declare(strict_types=1);

namespace App\Manager;

use App\DtoModel\HotelsSearchResultModel;
use App\Entity\Hotel;
use App\Exception\AppException;
use App\Repository\HotelRepository;
use App\Traits\EntityManagerTrait;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HotelManager
{
    use EntityManagerTrait;

    /**
     * @var HotelRepository
     */
    private HotelRepository $hotelRepository;

    /**
     * @var CacheInterface
     */
    private CacheInterface $hotelCache;

    /**
     * @param HotelRepository $hotelRepository
     * @param CacheInterface $hotelCache
     */
    public function __construct(HotelRepository $hotelRepository, CacheInterface $hotelCache)
    {
        $this->hotelRepository = $hotelRepository;
        $this->hotelCache = $hotelCache;
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
    public function getDetails(string $hotelId): Hotel
    {
        try {
            $hotel = $this->hotelCache->get($hotelId, function(ItemInterface $item) use ($hotelId) {
                $item->expiresAfter(3600*24);

                return $this->hotelRepository->find($hotelId);
            });
        } catch (InvalidArgumentException $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }

        if (!$hotel instanceof Hotel) {
            throw new AppException('Hotel not found', Response::HTTP_NOT_FOUND);
        }

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
     * @param string $id
     * @param string $name
     * @param string $description
     * @param int $costOneDay
     * @param string $address
     * @return Hotel
     * @throws AppException
     */
    public function edit(string $id, string $name, string $description, int $costOneDay, string $address): Hotel
    {
        $hotel = $this->get($id);

        $hotel->setName($name);
        $hotel->setDescription($description);
        $hotel->setCostOneDay($costOneDay);
        $hotel->setAddress($address);
        $this->entityManager->flush();

        return $hotel;
    }

    /**
     * @param array $filters
     * @return HotelsSearchResultModel
     */
    public function search(array $filters): HotelsSearchResultModel
    {
        $hotels = $this->hotelRepository->searchHotelsList($filters);
        $total = count($hotels);

        return new HotelsSearchResultModel($total, $hotels);
    }
}