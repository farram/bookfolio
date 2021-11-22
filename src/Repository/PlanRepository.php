<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Plan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plan[]    findAll()
 * @method Plan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findExistingPlan($planId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idPriceApi = :idPriceApi')
            ->setParameter('idPriceApi', $planId)
            ->orWhere('p.idPriceApiAnnual = :idPriceApiAnnual')
            ->setParameter('idPriceApiAnnual', $planId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
