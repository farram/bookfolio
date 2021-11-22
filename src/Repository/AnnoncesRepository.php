<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    public function findActive()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }



    public function findCountUploadThisMonth($user, $month)
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->andWhere('a.createdAt >= :date')
            ->setParameter('date', $month->format('Y-m-d 00:00:00'))
            ->getQuery()
            ->getSingleScalarResult();
    }


    /*
    public function findOneBySomeField($value): ?Annonces
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
