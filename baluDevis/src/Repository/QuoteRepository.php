<?php

namespace App\Repository;

use App\Entity\Quote;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quote>
 *
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }
    public function findValidQuotes()
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.status = :status')
            ->setParameter('status', 'valider')
            ->getQuery()
            ->getResult();
    }
    public function findSearch(Quote $quote, User $user) {
            $query = $this
                ->createQueryBuilder('q')
                ->select('q')
                ->leftJoin('q.client', 'c')
                ->leftJoin('q.userQuote', 'u'); // Assuming 'userQuote' is the relationship with the user entity

            if (!empty($quote->getStatus())) {
                $query = $query
                    ->andWhere('q.status = :status')
                    ->setParameter('status', $quote->getStatus());
            }

            if (!empty($quote->getCreatedAt())) {
                $query = $query
                    ->andWhere('q.createdAt >= :createdAt')
                    ->setParameter('createdAt', $quote->getCreatedAt());
            }

            if (!empty($quote->getExpiryAt())) {
                $query = $query
                    ->andWhere('q.expiryAt <= :expiryAt')
                    ->setParameter('expiryAt', $quote->getExpiryAt());
            }

            if ($user !== null) {
                $query = $query
                    ->andWhere('q.userQuote = :user')
                    ->setParameter('user', $user);
            }

            return $query;
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
