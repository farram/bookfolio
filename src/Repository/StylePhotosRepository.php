<?php

namespace App\Repository;

use App\Entity\StylePhotos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StylePhotos|null find($id, $lockMode = null, $lockVersion = null)
 * @method StylePhotos|null findOneBy(array $criteria, array $orderBy = null)
 * @method StylePhotos[]    findAll()
 * @method StylePhotos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StylePhotosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StylePhotos::class);
    }

    // /**
    //  * @return StylePhotos[] Returns an array of StylePhotos objects
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
    public function findOneBySomeField($value): ?StylePhotos
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
