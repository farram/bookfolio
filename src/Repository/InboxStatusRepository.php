<?php

namespace App\Repository;

use App\Entity\InboxStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InboxStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method InboxStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method InboxStatus[]    findAll()
 * @method InboxStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InboxStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InboxStatus::class);
    }

    // /**
    //  * @return InboxStatus[] Returns an array of InboxStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InboxStatus
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
