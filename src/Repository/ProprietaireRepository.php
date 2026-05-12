<?php

namespace App\Repository;

use App\Entity\Proprietaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProprietaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proprietaire::class);
    }

    // /**
    //  * 🔍 Trouver les propriétaires par nom
    //  */
    // public function findByName(string $name): array
    // {
    //    return $this->createQueryBuilder('p')
    //        ->andWhere('p.name LIKE :name')
    //        ->setParameter('name', '%'.$name.'%')
    //        ->orderBy('p.name', 'ASC')
    //        ->getQuery()
    //        ->getResult();
    // }

    /**
     * 🔍 Trouver les propriétaires par téléphone
     */
    // public function findByPhone(string $phone): ?Proprietaire
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.phone = :phone')
    //         ->setParameter('phone', $phone)
    //         ->getQuery()
    //         ->getOneOrNullResult();
    //}

    /**
     * 📊 Exemple statistique : nombre total de propriétaires
     */
    // public function countProprietaires(): int
    // {
    //     return $this->createQueryBuilder('p')
    //         ->select('COUNT(p.id)')
    //         ->getQuery()
    //         ->getSingleScalarResult();
    // }
}