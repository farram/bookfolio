<?php

namespace App\Repository;

use App\Entity\SortListFollow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SortListFollow|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortListFollow|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortListFollow[]    findAll()
 * @method SortListFollow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortListFollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortListFollow::class);
    }

    public function findAllSort()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.sort', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return SortListFollow[] Returns an array of SortListFollow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SortListFollow
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
