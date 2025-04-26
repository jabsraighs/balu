<?php

namespace App\Repository;

use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Company;

/**
 * @extends ServiceEntityRepository<Quote>
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    public function countByStatusAndCompany(string $status, Company $company): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->andWhere('q.status = :status')
            ->andWhere('q.company = :company')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'status' => $status,
                'company' => $company
            ]))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotalByDateRange(Company $company, \DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('q')
            ->select('SUM(q.totalAmount)')
            ->andWhere('q.company = :company')
            ->andWhere('q.dateCreated BETWEEN :from AND :to')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'company' => $company,
                'from' => $from,
                'to' => $to
            ]))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère le nombre total de devis
     */
    public function getTotalCount(): int
    {
        $qb = $this->createQueryBuilder('q')
            ->select('COUNT(q.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Calcule le taux de conversion des devis (% des devis acceptés)
     */
    public function getConversionRate(): float
    {
        $conn = $this->getEntityManager()->getConnection();

        $totalSql = "SELECT COUNT(id) FROM quote";
        $acceptedSql = "SELECT COUNT(id) FROM quote WHERE status = 'accepted'";

        $total = (int) $conn->executeQuery($totalSql)->fetchOne();
        $accepted = (int) $conn->executeQuery($acceptedSql)->fetchOne();

        if ($total === 0) {
            return 0;
        }

        return ($accepted / $total) * 100;
    }

    /**
     * Récupère les devis récents
     */
    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.client', 'c')
            ->addSelect('c')
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all quotes for a specific company
     */
    public function findByCompany(Company $company): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.company = :company')
            ->setParameter('company', $company)
            ->orderBy('q.dateCreated', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Quote[] Returns an array of Quote objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Quote
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
