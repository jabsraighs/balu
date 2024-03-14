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
            ->leftJoin('q.userQuote', 'u')
            ->where('q.userQuote = :user')
            ->setParameter('user', $user);
        // Check if the status parameter is set in the form submission
        if (!empty($quote->getStatus())) {
            // Assuming you want to filter quotes by their status
            $query = $this
                ->createQueryBuilder('q')
                ->andWhere('q.status LIKE :status')
                ->setParameter('status', $quote->getStatus());
        }

        // Add more conditions for other filters if needed

        $query = $query->getQuery();
        $result = $query->getResult();
        return $result;
    }

    /**
    * @return array Returns the number of validated quotes of the user
    */
    public function findQuotesAcceptedByUser(User $user): Array
    {
        $result = $this->createQueryBuilder('q')
            ->andWhere('q.status = :status')
            ->andWhere('q.userQuote = :user')
            ->setParameter('status', 'valider')
            ->setParameter('user', $user)
            ->getQuery()
            ->getScalarResult();

        return $result;
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
