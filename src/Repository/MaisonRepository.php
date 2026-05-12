<?php

namespace App\Repository;

use App\Entity\Maison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Maison>
 */
class MaisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maison::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByCity(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.city, COUNT(m.id) as count')
            ->groupBy('m.city')
            ->orderBy('count', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findDistinctCities(): array
    {
        $rows = $this->createQueryBuilder('m')
            ->select('DISTINCT m.city')
            ->orderBy('m.city', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($rows, 'city');
    }

    public function findBySearchCriteria(?string $city, ?float $minPrice, ?float $maxPrice): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($city) {
            $qb->andWhere('m.city = :city')
               ->setParameter('city', $city);
        }

        if ($minPrice !== null) {
            $qb->andWhere('m.price >= :minPrice')
               ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('m.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        return $qb->getQuery()->getResult();
    }

    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
