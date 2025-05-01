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
     * Récupère le montant total des factures payées
     * Si $company est fourni, limite aux factures de cette entreprise (pour ROLE_COMPANY ou ROLE_ACCOUNTANT)
     * Sinon retourne le total de toutes les factures payées (pour ROLE_ADMIN)
     */
    public function getTotalAmount(?Company $company = null): float
    {
        $qb = $this->createQueryBuilder('i')
            ->select('SUM(i.totalAmount)')
            ->where('i.status = :status')
            ->setParameter('status', 'paid');
            
        if ($company) {
            $qb->andWhere('i.company = :company')
               ->setParameter('company', $company);
        }
        
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
        $previousMonth = $currentMonth - 1 ?: 12;
        $currentYear = date('Y');
        $previousYear = $previousMonth == 12 ? $currentYear - 1 : $currentYear;

        $currentMonthSql = "
            SELECT COALESCE(SUM(total_amount), 0) 
            FROM invoice 
            WHERE EXTRACT(MONTH FROM created_at) = :currentMonth 
            AND EXTRACT(YEAR FROM created_at) = :currentYear
            AND status = 'paid'
        ";

        $previousMonthSql = "
            SELECT COALESCE(SUM(total_amount), 0) 
            FROM invoice 
            WHERE EXTRACT(MONTH FROM created_at) = :previousMonth 
            AND EXTRACT(YEAR FROM created_at) = :previousYear
            AND status = 'paid'
        ";

        $currentMonthTotal = (float) $conn->executeQuery($currentMonthSql, [
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear
        ])->fetchOne();

        $previousMonthTotal = (float) $conn->executeQuery($previousMonthSql, [
            'previousMonth' => $previousMonth,
            'previousYear' => $previousYear
        ])->fetchOne();

        if (!$previousMonthTotal || $previousMonthTotal == 0) {
            return $currentMonthTotal ? 100 : 0;
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

    /**
     * Récupère les revenus mensuels sur les 6 derniers mois
     * @return array Un tableau avec les revenus par mois
     */
    public function getMonthlyRevenueLastMonths(): array
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
        $monthlyRevenue = $this->getMonthlyRevenueLastMonths();
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

    public function getTotalInvoicedForPeriod(Company $c, int $year, int $month): float
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        $qb = $this->createQueryBuilder('i')
            ->select('COALESCE(SUM(i.totalAmount),0)')
            ->andWhere('i.company = :c')
            ->andWhere('i.createdAt BETWEEN :start AND :end')
            ->setParameter('c', $c) // Use the full company object
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate->setTime(23, 59, 59));

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function getTotalPaidForPeriod(Company $c, int $year, int $month): float
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        return (float) $this->getEntityManager()->createQuery(
            'SELECT COALESCE(SUM(p.amount),0)
             FROM App\Entity\Payment p
             JOIN p.invoice i
             WHERE i.company = :c
             AND p.datePaid BETWEEN :start AND :end'
        )
            ->setParameters([
                'c' => $c,
                'start' => $startDate,
                'end' => $endDate->setTime(23, 59, 59)
            ])->getSingleScalarResult();
    }

    public function getTotalPendingForPeriod(Company $c, int $year, int $month): float
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        $qb = $this->createQueryBuilder('i')
            ->select('COALESCE(SUM(i.totalAmount),0)')
            ->andWhere('i.company = :c')
            ->andWhere("i.status = 'pending'")
            ->andWhere('i.createdAt BETWEEN :start AND :end')
            ->setParameter('c', $c)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate->setTime(23, 59, 59));

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function getTotalOverdueForPeriod(Company $c, int $year, int $month): float
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        $qb = $this->createQueryBuilder('i')
            ->select('COALESCE(SUM(i.totalAmount),0)')
            ->andWhere('i.company = :c')
            ->andWhere("i.status = 'overdue'")
            ->andWhere('i.dateDue BETWEEN :start AND :end')
            ->setParameter('c', $c)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate->setTime(23, 59, 59));

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAveragePaymentTime(Company $c, int $year, int $month): float
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p.datePaid', 'i.dateDue')
            ->from('App\Entity\Payment', 'p')
            ->join('p.invoice', 'i')
            ->where('i.company = :c')
            ->andWhere('p.datePaid BETWEEN :start AND :end')
            ->setParameter('c', $c)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate->setTime(23, 59, 59));

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return 0;
        }

        $totalDays = 0;
        foreach ($results as $result) {
            $datePaid = $result['datePaid'];
            $dateDue = $result['dateDue'];

            if ($datePaid && $dateDue) {
                $diff = $datePaid->diff($dateDue);
                $totalDays += $diff->days;
            }
        }

        return count($results) > 0 ? $totalDays / count($results) : 0;
    }

    public function getFinancialReportData(): array
    {
        return [
            'totalPaid' => $this->createQueryBuilder('i')
                ->select('COALESCE(SUM(i.totalAmount), 0)')
                ->where('i.status = :paid')
                ->setParameter('paid', 'paid')
                ->getQuery()
                ->getSingleScalarResult(),
    
            'totalUnpaid' => $this->createQueryBuilder('i')
                ->select('COALESCE(SUM(i.totalAmount), 0)')
                ->where('i.status = :pending')
                ->setParameter('pending', 'pending')
                ->getQuery()
                ->getSingleScalarResult(),
    
            'averageInvoice' => $this->createQueryBuilder('i')
                ->select('COALESCE(AVG(i.totalAmount), 0)')
                ->getQuery()
                ->getSingleScalarResult(),
                
            'topClients' => $this->createQueryBuilder('i')
                ->select('c.name as client_name, COALESCE(SUM(i.totalAmount), 0) as total')
                ->join('i.client', 'c')
                ->groupBy('c.id')
                ->orderBy('total', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult(),
                
            'paymentMethods' => $this->getEntityManager()->createQueryBuilder()
                ->select('p.method as method, COUNT(p.id) as count')
                ->from('App\Entity\Payment', 'p')
                ->join('p.invoice', 'i')
                ->groupBy('p.method')
                ->getQuery()
                ->getResult(),
                
            'latestInvoices' => $this->createQueryBuilder('i')
                ->join('i.client', 'c')
                ->addSelect('c')
                ->orderBy('i.createdAt', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult()
        ];
    }

    public function getConversionRateForPeriod(Company $c, int $year, int $month): float
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        $qbTotal = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(q)')
            ->from('App\Entity\Quote', 'q')
            ->andWhere('q.company = :c')
            ->andWhere('q.createdAt BETWEEN :start AND :end')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'c' => $c,
                'start' => $startDate,
                'end' => $endDate->setTime(23, 59, 59)
            ]));

        $total = (int) $qbTotal->getQuery()->getSingleScalarResult();

        $qbConv = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(i)')
            ->from('App\Entity\Invoice', 'i')
            ->andWhere('i.company = :c')
            ->andWhere('i.createdAt BETWEEN :start AND :end')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'c' => $c,
                'start' => $startDate,
                'end' => $endDate->setTime(23, 59, 59)
            ]));

        $converted = (int) $qbConv->getQuery()->getSingleScalarResult();

        return $total > 0 ? $converted / $total : 0;
    }

    public function getMonthlyRevenueForYear(Company $c, int $year): array
    {
        $data = array_fill(1, 12, 0.0);

        // On va faire une requête pour chaque mois (ce n'est pas optimal mais c'est plus simple)
        for ($month = 1; $month <= 12; $month++) {
            $startDate = new \DateTime("$year-$month-01");
            $endDate = clone $startDate;
            $endDate->modify('last day of this month')->setTime(23, 59, 59);

            $result = $this->createQueryBuilder('i')
                ->select("SUM(i.totalAmount) as s")
                ->andWhere('i.company = :c')
                ->andWhere('i.createdAt BETWEEN :start AND :end')
                ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                    'c' => $c,
                    'start' => $startDate,
                    'end' => $endDate
                ]))
                ->getQuery()
                ->getSingleScalarResult();

            $data[$month] = $result ? (float) $result : 0.0;
        }

        return $data;
    }

    public function findByPeriod(Company $c, int $year, int $month): array
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;

        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }

        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.company = :c')
            ->andWhere('i.createdAt BETWEEN :start AND :end')
            ->setParameters(new \Doctrine\Common\Collections\ArrayCollection([
                'c' => $c,
                'start' => $startDate,
                'end' => $endDate->setTime(23, 59, 59)
            ]));

        return $qb->orderBy('i.createdAt', 'DESC')->getQuery()->getResult();
    }
}
