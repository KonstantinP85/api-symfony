<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Booking;
use App\Exception\AppException;
use App\Exception\DateTimeException;
use App\Repository\BookingRepository;
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
     * @var BookingRepository
     */
    private BookingRepository $bookingRepository;

    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @param HotelManager $hotelManager
     * @param BookingRepository $bookingRepository
     */
    public function __construct(HotelManager $hotelManager, BookingRepository $bookingRepository)
    {
        $this->hotelManager = $hotelManager;
        $this->bookingRepository = $bookingRepository;
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
        $arrivalTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $arrivalTime);
        if ($arrivalTime === false) {
            throw new DateTimeException('Not correct date format', Response::HTTP_BAD_REQUEST);
        }
        $booking = new Booking($hotel, $user, $arrivalTime, $duration);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }

    /**
     * @param string $bookingId
     * @return Booking
     * @throws AppException
     */
    public function get(string $bookingId): Booking
    {
        $booking = $this->bookingRepository->find($bookingId);
        if (!$booking instanceof Booking) {
            throw new AppException('Booking is not found', Response::HTTP_NOT_FOUND);
        }

        return $booking;
    }
}