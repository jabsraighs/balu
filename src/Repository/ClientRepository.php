<?php

namespace App\Repository;

use App\Entity\Client;
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
