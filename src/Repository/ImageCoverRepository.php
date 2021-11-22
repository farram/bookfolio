<?php

namespace App\Repository;

use App\Entity\ImageCover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageCover|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageCover|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageCover[]    findAll()
 * @method ImageCover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageCoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageCover::class);
    }

    // /**
    //  * @return ImageCover[] Returns an array of ImageCover objects
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
    public function findOneBySomeField($value): ?ImageCover
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
