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
            ->setParameter('userId', $user->getId())
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
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
