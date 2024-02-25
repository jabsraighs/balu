<?php

namespace App\Repository;

use App\Entity\QuoteLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuoteLine>
 *
 * @method QuoteLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuoteLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuoteLine[]    findAll()
 * @method QuoteLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteLine::class);
    }

//    /**
//     * @return QuoteLine[] Returns an array of QuoteLine objects
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

//    public function findOneBySomeField($value): ?QuoteLine
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
