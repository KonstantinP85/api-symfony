<?php

declare(strict_types=1);

namespace App\DtoModel\Booking;

use App\DtoModel\BaseApiDtoModel;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBookingDtoModel extends BaseApiDtoModel
{
    /**
     * @var string
     * @Assert\NotBlank(message="HotelId is required")
     */
    public string $hotelId;

    /**
     * @var string
     * @Assert\NotBlank(message="Arrival time is required")
     */
    public string $arrivalTime;

    /**
     * @var int
     * @Assert\NotBlank(message="Duration is required")
     */
    public int $duration;
}