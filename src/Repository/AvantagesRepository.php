<?php

namespace App\Repository;

use App\Entity\Avantages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Avantages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avantages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avantages[]    findAll()
 * @method Avantages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvantagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avantages::class);
    }

    // /**
    //  * @return Avantages[] Returns an array of Avantages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Avantages
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
