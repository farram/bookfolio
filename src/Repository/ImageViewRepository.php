<?php

namespace App\Repository;

use App\Entity\ImageView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageView|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageView|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageView[]    findAll()
 * @method ImageView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageView::class);
    }

    public function findThisMonthByUser($image)
    {
        return $this->createQueryBuilder('i')
            ->select('count(i.ipAddress) AS count, i.createdAt')
            ->andWhere('i.image = :image')
            ->setParameter('image', $image)

            ->andwhere('MONTH(i.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(i.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('i.createdAt')
            ->orderBy('i.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findThisYearByImage($image)
    {
        return $this->createQueryBuilder('i')
            ->select('count(i.ipAddress) AS count, i.createdAt, MONTH(i.createdAt) AS month')
            ->andWhere('i.image = :image')
            ->setParameter('image', $image)

            // ->andwhere('YEAR(i.createdAt) = :year')
            // ->setParameter('year', $image->getCreatedAt()->format('Y'))

            ->groupBy('month')
            ->orderBy('i.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findCountThisYearByUser($image)
    {
        return $this->createQueryBuilder('i')
            ->select('count(i.image)')
            ->andWhere('i.image = :image')
            ->setParameter('image', $image)

            ->andwhere('YEAR(i.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findMostView()
    {
        return $this->createQueryBuilder('iv')
            ->select('COUNT(iv.image) AS count')
            //->groupBy("i.image")
            ->orderBy('count', 'ASC')
            ->getQuery();
    }
}
