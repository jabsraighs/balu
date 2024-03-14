<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }
    public function findUsersByClientId($clientId)
    {
        return $this->createQueryBuilder('u')
            ->join('u.userClient', 'c') // Assurez-vous que la relation entre User et Client est correctement définie dans votre entité User
            ->where('c.id = :clientId')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    public function getNewClientsByUser(User $user): array
    {
        $currentMonth = (new \DateTime())->format('Y-m');
        $startDate = new \DateTime("$currentMonth-01 00:00:00");
        $endDate = new \DateTime("$currentMonth-" . date('t', strtotime($currentMonth)) . " 23:59:59");

        return $this->createQueryBuilder('c')
            ->andWhere('c.userClient = :user')
            ->andWhere('c.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getScalarResult();
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
