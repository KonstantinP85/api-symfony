<?php

declare(strict_types=1);

namespace App\DtoModel;

class HotelsSearchResultModel
{
    /**
     * @var int
     */
    public int $total;

    /**
     * @var array
     */
    public array $hotels;

    /**
     * @param int $total
     * @param array $hotels
     */
    public function __construct(int $total, array $hotels)
    {
        $this->hotels = $hotels;
        $this->total = $total;
    }
}