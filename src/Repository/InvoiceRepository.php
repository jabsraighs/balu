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

    /**
     * Renvoie un tableau ['paid'=>X, 'pending'=>Y, 'partial'=>Z]
     * pour la répartition des statuts de toutes les factures de la company
     */
    public function getStatusDistribution(Company $company): array
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i.status AS st, COUNT(i.id) AS cnt')
            ->andWhere('i.company = :company')
            ->setParameter('company', $company)
            ->groupBy('i.status');

        $raw = $qb->getQuery()->getResult();
        $dist = ['paid' => 0, 'pending' => 0, 'partial' => 0];
        foreach ($raw as $r) {
            $dist[$r['st']] = (int) $r['cnt'];
        }
        return $dist;
    }

    public function sumTotalByClient(Company $c): array
    {
        $qb = $this->createQueryBuilder('i')
            ->select('c.name as client, SUM(i.totalAmount) as total')
            ->join('i.client', 'c')
            ->andWhere('i.company = :c')
            ->groupBy('c.id')
            ->setParameter('c', $c);

        $data = [];
        foreach ($qb->getQuery()->getResult() as $row) {
            $data[$row['client']] = (float) $row['total'];
        }
        return $data;
    }

    public function findAllForCompany(Company $c): array
    {
        return $this->findBy(['company' => $c], ['dateCreated' => 'DESC']);
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
     * Rend un tableau ['YYYY-MM' => totalAmount, …] pour les X derniers mois
     */
    public function getMonthlyRevenue(Company $company, int $months = 6): array
    {
        $qb = $this->createQueryBuilder(alias: 'i')
            ->select("to_char(i.createdAt, 'YYYY-MM') AS ym, SUM(i.totalAmount) AS total")
            ->andWhere('i.company = :company')
            ->setParameter('company', $company)
            ->andWhere('i.createdAt >= :start')
            ->setParameter('start', (new \DateTime())->modify("-{$months} months"))
            ->groupBy('ym')
            ->orderBy('ym', 'ASC');

        $raw = $qb->getQuery()->getResult();
        $data = [];
        // initialiser tous les mois à 0
        for ($i = $months; $i >= 0; $i--) {
            $m = (new \DateTime())->modify("-{$i} months")->format('Y-m');
            $data[$m] = 0.0;
        }
        // injecter les valeurs existantes
        foreach ($raw as $r) {
            $data[$r['ym']] = (float) $r['total'];
        }
        return $data;
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
