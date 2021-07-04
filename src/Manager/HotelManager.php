<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Hotel;
use App\Traits\EntityManagerTrait;

class HotelManager
{
    use EntityManagerTrait;

    public function create(string $name, string $description, int $costOneDay, string $address): Hotel
    {
        $hotel = new Hotel($name, $description, $costOneDay, $address);
        $this->entityManager->persist($hotel);
        $this->entityManager->flush();

        return $hotel;
    }
}