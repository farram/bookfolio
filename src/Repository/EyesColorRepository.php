<?php

namespace App\Repository;

use App\Entity\EyesColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EyesColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method EyesColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method EyesColor[]    findAll()
 * @method EyesColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EyesColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EyesColor::class);
    }

    // /**
    //  * @return EyesColor[] Returns an array of EyesColor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EyesColor
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
