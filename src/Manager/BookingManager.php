<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Booking;
use App\Exception\AppException;
use App\Exception\DateTimeException;
use App\Traits\EntityManagerTrait;
use App\Traits\UserTokenStorageTrait;
use Symfony\Component\HttpFoundation\Response;

class BookingManager
{
    use UserTokenStorageTrait;
    use EntityManagerTrait;

    /**
     * @var HotelManager
     */
    private HotelManager $hotelManager;

    /**
     * BookingManager constructor.
     * @param HotelManager $hotelManager
     */
    public function __construct(HotelManager $hotelManager)
    {
        $this->hotelManager = $hotelManager;
    }

    /**
     * @param string $hotelId
     * @param string $arrivalTime
     * @param int $duration
     * @return Booking
     * @throws AppException
     */
    public function create(string $hotelId, string $arrivalTime, int $duration): Booking
    {
        $user = $this->getLoggedInUser();
        $hotel = $this->hotelManager->get($hotelId);
        $arrivalTime = \DateTimeImmutable::createFromFormat($arrivalTime, 'd.m.Y H:i:s');
        if ($arrivalTime === false) {
            throw new DateTimeException('Not correct date format', Response::HTTP_BAD_REQUEST);
        }
        $booking = new Booking($hotel, $user, $arrivalTime, $duration);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }
}