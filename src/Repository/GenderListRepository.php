<?php

namespace App\Repository;

use App\Entity\GenderList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GenderList|null find($id, $lockMode = null, $lockVersion = null)
 * @method GenderList|null findOneBy(array $criteria, array $orderBy = null)
 * @method GenderList[]    findAll()
 * @method GenderList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenderListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenderList::class);
    }

    // /**
    //  * @return GenderList[] Returns an array of GenderList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GenderList
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
