<?php

namespace App\Repository;

use App\Entity\ImageHeartstroke;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageHeartstroke|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageHeartstroke|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageHeartstroke[]    findAll()
 * @method ImageHeartstroke[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageHeartstrokeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageHeartstroke::class);
    }

    public function findLimit($limit)
    {
        return $this->createQueryBuilder('i')
            ->setMaxResults($limit)
            ->orderBy('i.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNoLimit()
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.position', 'ASC')
            ->getQuery();
    }
}
