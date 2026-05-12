<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Maison;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPaid(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.paye = :paye')
            ->setParameter('paye', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countPending(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.paye = :paye')
            ->setParameter('paye', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.dateDebut', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findMostReservedMaisons()
    {
        $sql = "SELECT m.title, COUNT(r.id) AS howMany
                FROM reservation r
                INNER JOIN maison m ON m.id = r.maison_id
                GROUP BY m.title
                ORDER BY howMany DESC";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        return $stmt->executeQuery()->fetchAllAssociative();
    }

    public function findByMaison(Maison $maison): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.maison = :maison')
            ->setParameter('maison', $maison)
            ->orderBy('r.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyRevenue(): array
    {
        $sql = "SELECT MONTH(r.date_debut) as month, SUM(m.price * DATEDIFF(r.date_fin, r.date_debut)) as revenue
                FROM reservation r
                INNER JOIN maison m ON m.id = r.maison_id
                WHERE r.paye = 1
                GROUP BY MONTH(r.date_debut)
                ORDER BY month ASC";

        $conn = $this->getEntityManager()->getConnection();
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    public function getTotalRevenue(): float
    {
        $sql = "SELECT COALESCE(SUM(m.price * DATEDIFF(r.date_fin, r.date_debut)), 0) as total
                FROM reservation r
                INNER JOIN maison m ON m.id = r.maison_id
                WHERE r.paye = 1";

        $conn = $this->getEntityManager()->getConnection();
        $result = $conn->executeQuery($sql)->fetchAssociative();
        return (float) $result['total'];
    }

    public function countUpcoming(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.dateDebut > :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countThisMonth()
{
    $startOfMonth = new \DateTime('first day of this month 00:00:00');
    $endOfMonth = new \DateTime('last day of this month 23:59:59');

    return $this->createQueryBuilder('r')
        ->select('COUNT(r.id)')
        ->where('r.dateDebut BETWEEN :start AND :end')
        ->setParameter('start', $startOfMonth)
        ->setParameter('end', $endOfMonth)
        ->getQuery()
        ->getSingleScalarResult();
}

    public function getMonthlyRevenueData(): array
{
    // Récupérer les réservations payées
    $results = $this->createQueryBuilder('r')
        ->select('r.dateDebut', 'm.price')
        ->join('r.maison', 'm')
        ->where('r.paye = :paid')
        ->setParameter('paid', true)
        ->getQuery()
        ->getResult();

    $data = array_fill(0, 12, 0);

    foreach ($results as $res) {
        if ($res['dateDebut'] instanceof \DateTimeInterface) {
            $month = (int)$res['dateDebut']->format('n') - 1; // n donne 1-12
            $data[$month] += (float)$res['price'];
        }
    }

    return $data;
}

}
