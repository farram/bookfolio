<?php

namespace App\Repository;

use App\Entity\NotSuggested;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotSuggested|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotSuggested|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotSuggested[]    findAll()
 * @method NotSuggested[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotSuggestedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotSuggested::class);
    }

    // /**
    //  * @return NotSuggested[] Returns an array of NotSuggested objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotSuggested
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
