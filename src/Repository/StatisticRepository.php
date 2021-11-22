<?php

namespace App\Repository;

use App\Entity\Statistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Statistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statistic[]    findAll()
 * @method Statistic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistic::class);
    }

    public function findTodayByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.ipAddress) AS count, s.createdAt')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('DAY(s.createdAt) = :day')
            ->setParameter('day', date('d'))

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('s.createdAt')
            ->orderBy('s.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findCountTodayByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.user)')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('DAY(s.createdAt) = :day')
            ->setParameter('day', date('d'))

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findYesterdayByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.ipAddress) AS count, s.createdAt')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('DAY(s.createdAt) = :day')
            ->setParameter('day', (date('d') - 1))

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('s.createdAt')
            ->orderBy('s.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findCountYesterdayByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.user)')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('DAY(s.createdAt) = :day')
            ->setParameter('day', (date('d') - 1))

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findThisMonthByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.ipAddress) AS count, s.createdAt')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('s.createdAt')
            ->orderBy('s.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findCountThisMonthByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.user)')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLastMonthByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.ipAddress) AS count, s.createdAt')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', (date('m') - 1))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('s.createdAt')
            ->orderBy('s.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findCountLastMonthByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.user)')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('MONTH(s.createdAt) = :month')
            ->setParameter('month', (date('m') - 1))

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findThisYearByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.ipAddress) AS count, s.createdAt, MONTH(s.createdAt) AS month')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('month')
            ->orderBy('s.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findCountThisYearByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.user)')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findCountLastYearByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.user)')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', (date('Y') - 1))

            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findLastYearByUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.ipAddress) AS count, s.createdAt')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)

            ->andwhere('YEAR(s.createdAt) = :year')
            ->setParameter('year', (date('Y') - 1))

            ->groupBy('s.createdAt')
            ->orderBy('s.createdAt')

            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Statistic[] Returns an array of Statistic objects
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
    public function findOneBySomeField($value): ?Statistic
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
