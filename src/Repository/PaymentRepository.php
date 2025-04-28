<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function countByStatusAndCompany(string $status, Company $company): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status = :status')
            ->andWhere('p.company = :company')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'status' => $status,
                'company' => $company
            ]))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotalByDateRange(Company $company, \DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->andWhere('p.company = :company')
            ->andWhere('p.dateCreated BETWEEN :from AND :to')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'company' => $company,
                'from' => $from,
                'to' => $to
            ]))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouver tous les paiements d'une entreprise
     */
    public function findByCompany(Company $company): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.invoice', 'i')
            ->andWhere('i.company = :company')
            ->setParameter('company', $company)
            ->orderBy('p.datePaid', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Somme des paiements reçus pour une période
     */
    public function sumPaymentsByPeriod(Company $company, \DateTimeInterface $startDate, \DateTimeInterface $endDate): float
    {
        $result = $this->createQueryBuilder('p')
            ->select('SUM(p.amount)')
            ->join('p.invoice', 'i')
            ->andWhere('i.company = :company')
            ->andWhere('p.datePaid BETWEEN :startDate AND :endDate')
            ->setParameter('company', $company)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0;
    }

    //    /**
    //     * @return Payment[] Returns an array of Payment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Payment
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
