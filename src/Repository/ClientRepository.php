<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Récupère le nombre total de clients
     */
    public function getTotalCount(): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Récupère le nombre de nouveaux clients ce mois-ci
     */
    public function getNewClientsCount(): int
    {
        $firstDayOfMonth = new \DateTime('first day of this month midnight');

        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.createdAt >= :firstDayOfMonth')
            ->setParameter('firstDayOfMonth', $firstDayOfMonth);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getTopClientsByRevenue(Company $c, int $limit, int $year, int $month): array
    {
        $startDate = new \DateTime("$year-" . ($month > 0 ? "$month" : "01") . "-01");
        $endDate = clone $startDate;
        
        if ($month > 0) {
            $endDate->modify('last day of this month');
        } else {
            $endDate->modify('last day of december');
        }
        
        $qb = $this->createQueryBuilder('cl')
            ->select('cl.name as name, COUNT(i.id) as invoice_count, SUM(i.totalAmount) as total_amount')
            ->join('cl.invoices', 'i')
            ->andWhere('cl.company = :c')
            ->andWhere('i.dateCreated BETWEEN :start AND :end')
            ->setParameter('c', $c)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate->setTime(23, 59, 59));
            
        $raw = $qb->groupBy('cl.id')
            ->orderBy('total_amount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();

        return array_map(function ($r) {
            return [
                'name' => $r['name'],
                'revenue' => $r['total_amount'],
                'invoiceCount' => $r['invoice_count']
            ]; 
        }, $raw);
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
