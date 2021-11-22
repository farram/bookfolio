<?php

namespace App\Repository;

use App\Entity\ReleaseNotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReleaseNotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReleaseNotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReleaseNotes[]    findAll()
 * @method ReleaseNotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReleaseNotesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReleaseNotes::class);
    }

    public function findActiveBy()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findActiveByQuery()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery();
    }

    // /**
    //  * @return ReleaseNotes[] Returns an array of ReleaseNotes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReleaseNotes
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
