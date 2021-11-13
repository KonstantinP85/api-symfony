<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HotelRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotel::class);
    }

    /**
     * @param array<string, string> $filters
     * @return array|Hotel[]
     */
    public function searchHotelsList(array $filters): array
    {
        $qb = $this->createQueryBuilder('h');

        if (array_key_exists('hotelName', $filters)) {
            $qb->andWhere($qb->expr()->like('h.name', ':hotelName'))
                ->setParameter('hotelName', ' % ' . $filters['hotelName'] . ' % ');
        }
        if (array_key_exists('address', $filters)) {
            $qb->andWhere($qb->expr()->like('h.address', ':address'))
                ->setParameter('address', ' % ' . $filters['address'] . ' % ');
        }
        if (array_key_exists('costOneDay', $filters)) {
            $qb->andWhere($qb->expr()->like('h.costOneDay', ':costOneDay'))
                ->setParameter('costOneDay', ' % ' . $filters['costOneDay'] . ' % ');
        }

        return $qb->getQuery()->getResult();
    }
}