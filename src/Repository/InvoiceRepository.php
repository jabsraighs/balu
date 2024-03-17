<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }
     public function findValidInvoices()
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.paymentStatus = :status')
            ->setParameter('status', 'valider')
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyRevenueByUser(User $user)
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        // Calculate the start and end date of the current month
        $startDate = new \DateTime("$currentYear-$currentMonth-01");
        $endDate = new \DateTime("$currentYear-$currentMonth-" . date('t', strtotime("$currentYear-$currentMonth")));

        $result = $this->createQueryBuilder('i')
            ->innerJoin('i.userInvoice','user')
            ->select("SUM(i.totalAmount)")
            ->where('user.id = :userId')
            ->andWhere('i.createdAt BETWEEN :startDate AND :endDate')
            ->andWhere('i.paymentStatus = :status')
            ->setParameter('userId', $user->getId())
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', "valider")
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function getAnnualRevenueByUser(User $user): ?float
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.totalAmount)')
            ->andWhere('i.userInvoice = :user')
            ->andWhere('i.paymentStatus = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'valider')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getEveryMonthlyRevenue(User $user): array
    {
        $result = $this->createQueryBuilder('i')
            ->select('MONTH(i.createdAt) as month', 'SUM(i.totalAmount) as totalAmount')
            ->andWhere('i.userInvoice = :user')
            ->andWhere('i.paymentStatus = :status')
            ->groupBy('month')
            ->setParameter('user', $user)
            ->setParameter('status', 'valider')
            ->getQuery()
            ->getResult();

        $monthlyRevenue = [];
        foreach ($result as $row) {
            $month = $row['month'];
            $totalAmount = $row['totalAmount'];
            $monthlyRevenue[$month] = $totalAmount;
        }

        return $monthlyRevenue;
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
