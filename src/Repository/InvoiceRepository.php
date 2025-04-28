<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function countByStatusAndCompany(string $status, Company $company): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.status = :status')
            ->andWhere('i.company = :company')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'status' => $status,
                'company' => $company
            ]))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findOverdueInvoices(): array
    {
        $today = new \DateTime();

        return $this->createQueryBuilder('i')
            ->andWhere('i.status = :status')
            ->andWhere('i.dateDue < :today')
            ->setParameter('status', 'pending')
            ->setParameter('today', $today)
            ->orderBy('i.dateDue', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getOverdueAmount(): float
    {
        $today = new \DateTime();

        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.totalAmount)')
            ->where('i.status = :status')
            ->andWhere('i.dateDue < :today')
            ->setParameter('status', 'pending')
            ->setParameter('today', $today);

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    public function sumTotalByDateRange(Company $company, \DateTimeInterface $from, \DateTimeInterface $to): float
    {
        return (float) $this->createQueryBuilder('i')
            ->select('SUM(i.totalAmount)')
            ->andWhere('i.company = :company')
            ->andWhere('i.dateCreated BETWEEN :from AND :to')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'company' => $company,
                'from' => $from,
                'to' => $to
            ]))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère le montant total de toutes les factures payées
     */
    public function getTotalAmount(): float
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.totalAmount)')
            ->where('i.status = :status')
            ->setParameter('status', 'paid');

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Récupère le nombre de factures en attente de paiement
     */
    public function getPendingCount(): int
    {
        $qb = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.status = :status')
            ->setParameter('status', 'pending');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Récupère le montant total des factures en attente de paiement
     */
    public function getPendingAmount(): float
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.totalAmount)')
            ->where('i.status = :status')
            ->setParameter('status', 'pending');

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }

    /**
     * Calcule le pourcentage d'augmentation du chiffre d'affaires ce mois-ci par rapport au mois précédent
     */
    public function getMonthlyIncreasePercentage(): float
    {
        $conn = $this->getEntityManager()->getConnection();

        $currentMonth = date('m');
        $previousMonth = $currentMonth - 1 ?: 12; // Si on est en janvier, prendre décembre
        $currentYear = date('Y');
        $previousYear = $previousMonth == 12 ? $currentYear - 1 : $currentYear;

        // Requête pour le mois courant
        $currentMonthSql = "
        SELECT SUM(total_amount) 
        FROM invoice 
        WHERE EXTRACT(MONTH FROM date_due) = :currentMonth 
        AND EXTRACT(YEAR FROM date_due) = :currentYear
        AND status = 'paid'
    ";

        $previousMonthSql = "
        SELECT SUM(total_amount) 
        FROM invoice 
        WHERE EXTRACT(MONTH FROM date_due) = :previousMonth 
        AND EXTRACT(YEAR FROM date_due) = :previousYear
        AND status = 'paid'
    ";


        $currentMonthTotal = $conn->executeQuery($currentMonthSql, [
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear
        ])->fetchOne();

        $previousMonthTotal = $conn->executeQuery($previousMonthSql, [
            'previousMonth' => $previousMonth,
            'previousYear' => $previousYear
        ])->fetchOne();

        if (!$previousMonthTotal || $previousMonthTotal == 0) {
            return $currentMonthTotal ? 100 : 0; // Si pas de CA le mois dernier, c'est une augmentation de 100%
        }

        return (($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100;
    }

    /**
     * Récupère les revenus mensuels sur les 6 derniers mois
     * @return array Un tableau avec les revenus par mois
     */
    public function getMonthlyRevenue(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
        SELECT 
            EXTRACT(MONTH FROM date_due) as month, 
            EXTRACT(YEAR FROM date_due) as year,
            SUM(total_amount) as amount
        FROM invoice
        WHERE status = 'paid'
        AND date_due >= CURRENT_DATE - INTERVAL '5 months'
        GROUP BY EXTRACT(YEAR FROM date_due), EXTRACT(MONTH FROM date_due)
        ORDER BY year ASC, month ASC
    ";


        $months = $conn->executeQuery($sql)->fetchAllAssociative();

        $result = [];
        $monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        foreach ($months as $month) {
            $result[] = [
                'label' => $monthNames[$month['month'] - 1],
                'amount' => (float) $month['amount']
            ];
        }

        while (count($result) < 6) {
            array_unshift($result, ['label' => '---', 'amount' => 0]);
        }

        return $result;
    }

    public function getMaxMonthlyRevenue(): float
    {
        $monthlyRevenue = $this->getMonthlyRevenue();
        $max = 0;

        foreach ($monthlyRevenue as $month) {
            if ($month['amount'] > $max) {
                $max = $month['amount'];
            }
        }

        return max(ceil($max / 1000) * 1000, 1);
    }

    public function findRecent(int $limit): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.client', 'c')
            ->addSelect('c')
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Invoice[] Returns an array of Invoice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Invoice
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
