<?php

namespace App\Repository;

use App\Entity\Follow;
use App\Entity\Images;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Follow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Follow|null findOneBy(array $criteria, array $orderBy = null)
 * @method Follow[]    findAll()
 * @method Follow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Follow::class);
    }

    public function findMyFeed($user)
    {
        /* select i.* from images i left join follow F on i.user_id = f.friend_id where f.user_id = 1804  */

        return $this->createQueryBuilder('f')
            //->leftJoin('f.user', 'i', Expr\Join::WITH, 'i.user = f.user')
            //->join(Images::class, 'i', \Doctrine\ORM\Query\Expr\Join::WITH, 'i.user = f.user')
            ->leftJoin('App:Images', 'i', 'WITH', 'i.user = f.friend')
            ->where('f.user = :user')
            ->setParameter('user', $user)

            ->getQuery();
    }

    public function findAllFollowers($user, $sortBy)
    {
        $query = $this->createQueryBuilder('f');
        $query->where('f.friend = :friend');
        $query->setParameter('friend', $user);

        if ('createdAt' != $sortBy) {
            $query->leftJoin('f.user', 'u');
            $query->orderBy('u.'.$sortBy.'', 'ASC');
        } else {
            $query->orderBy('f.'.$sortBy.'', 'DESC');
        }

        $query->getQuery();

        return $query;
    }

    public function findAllFollowing($user, $sortBy)
    {
        $query = $this->createQueryBuilder('f');
        $query->where('f.user = :user');
        $query->setParameter('user', $user);

        if ('createdAt' != $sortBy) {
            $query->leftJoin('f.friend', 'u');
            $query->orderBy('u.'.$sortBy.'', 'ASC');
        } else {
            $query->orderBy('f.'.$sortBy.'', 'DESC');
        }

        $query->getQuery();

        return $query;
    }

    public function findAllFollowingBy($user, $profession, $experience, $location)
    {
        $query = $this->createQueryBuilder('f');
        $query->where('f.user = :user');
        $query->setParameter('user', $user);

        $query->getQuery();

        return $query;
    }

    // /**
    //  * @return Follow[] Returns an array of Follow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Follow
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
