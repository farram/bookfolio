<?php

namespace App\Repository;

use App\Entity\HairColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HairColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method HairColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method HairColor[]    findAll()
 * @method HairColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HairColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HairColor::class);
    }

    // /**
    //  * @return HairColor[] Returns an array of HairColor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HairColor
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
