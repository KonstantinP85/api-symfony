<?php

declare(strict_types=1);

namespace App\DtoModel\Hotel;

use App\DtoModel\BaseApiDtoModel;
use Symfony\Component\Validator\Constraints as Assert;

class CreateHotelDtoModel extends BaseApiDtoModel
{
    /**
     * @var string
     * @Assert\NotBlank(message="Name is required")
     */
    public string $name;

    /**
     * @var string
     * @Assert\NotBlank(message="Description is required")
     */
    public string $description;

    /**
     * @var int
     * @Assert\NotBlank(message="Cost one day is required")
     */
    public int $costOneDay;

    /**
     * @var string
     * @Assert\NotBlank(message="Address is required")
     */
    public string $address;
}