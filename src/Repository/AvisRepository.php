<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Avis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avis[]    findAll()
 * @method Avis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function findByLimit($limit)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->andWhere('a.isSelected = :isSelected')
            ->setParameter('isSelected', true)
            ->orderBy('a.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findActiveByQuery()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery();
    }
}
