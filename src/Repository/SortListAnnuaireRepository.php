<?php

namespace App\Repository;

use App\Entity\SortListAnnuaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SortListAnnuaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortListAnnuaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortListAnnuaire[]    findAll()
 * @method SortListAnnuaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortListAnnuaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortListAnnuaire::class);
    }

    public function findAllSort()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.sort', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return SortListAnnuaire[] Returns an array of SortListAnnuaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SortListAnnuaire
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
