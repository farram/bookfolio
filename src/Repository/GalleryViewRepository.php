<?php

namespace App\Repository;

use App\Entity\GalleryView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GalleryView|null find($id, $lockMode = null, $lockVersion = null)
 * @method GalleryView|null findOneBy(array $criteria, array $orderBy = null)
 * @method GalleryView[]    findAll()
 * @method GalleryView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GalleryView::class);
    }

    // /**
    //  * @return GalleryView[] Returns an array of GalleryView objects
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
    public function findOneBySomeField($value): ?GalleryView
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
