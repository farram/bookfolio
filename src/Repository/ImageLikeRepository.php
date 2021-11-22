<?php

namespace App\Repository;

use App\Entity\ImageLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageLike[]    findAll()
 * @method ImageLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageLike::class);
    }

    // /**
    //  * @return ImageLike[] Returns an array of ImageLike objects
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
    public function findOneBySomeField($value): ?ImageLike
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
