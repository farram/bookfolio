<?php

namespace App\Repository;

use App\Entity\Physical;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Physical|null find($id, $lockMode = null, $lockVersion = null)
 * @method Physical|null findOneBy(array $criteria, array $orderBy = null)
 * @method Physical[]    findAll()
 * @method Physical[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhysicalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Physical::class);
    }

    // /**
    //  * @return Physical[] Returns an array of Physical objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Physical
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
