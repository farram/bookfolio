<?php

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function findAllUser($user)
    {
        return $this->createQueryBuilder('g')
            ->where('g.user = :user')
            ->setParameter('user', $user)
            ->orderBy('g.position', 'ASC')
            ->getQuery();
    }

    public function findWithImages($book)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.images', 'i')
            ->where('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)

            ->andWhere('g.user = :user')
            ->setParameter('user', $book->getUser())
            ->andWhere('g.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('g.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCountGalleryAddThisMonth($user, $month)
    {
        return $this->createQueryBuilder('g')
            ->select('COUNT(g)')
            ->andWhere('g.user = :user')
            ->setParameter('user', $user)
            ->andWhere('g.createdAt >= :date')
            ->setParameter('date', $month->format('Y-m-d 00:00:00'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
