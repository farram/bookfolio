<?php

namespace App\Repository;

use App\Entity\PlanDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlanDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanDetails[]    findAll()
 * @method PlanDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanDetails::class);
    }

    // /**
    //  * @return PlanDetails[] Returns an array of PlanDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlanDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
