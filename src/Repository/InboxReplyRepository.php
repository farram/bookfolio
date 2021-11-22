<?php

namespace App\Repository;

use App\Entity\InboxReply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InboxReply|null find($id, $lockMode = null, $lockVersion = null)
 * @method InboxReply|null findOneBy(array $criteria, array $orderBy = null)
 * @method InboxReply[]    findAll()
 * @method InboxReply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InboxReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InboxReply::class);
    }

    // /**
    //  * @return InboxReply[] Returns an array of InboxReply objects
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
    public function findOneBySomeField($value): ?InboxReply
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
