<?php

namespace App\Repository;

use App\Entity\ImageComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageComment|null findOneBy(array $criteria, array $orderBy = desc)
 * @method ImageComment[]    findAll()
 * @method ImageComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageComment::class);
    }

    public function findActiveBy($image)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.image = :image')
            ->setParameter('image', $image)
            ->andWhere('i.isActive = :active')
            ->setParameter('active', true)
            ->andWhere('i.parent IS NULL')

            ->orderBy('i.id', 'DESC')
            ->getQuery();
    }

    public function findAnswerBy($image, $comment)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.image = :image')
            ->setParameter('image', $image)

            ->andWhere('i.isActive = :active')
            ->setParameter('active', true)

            ->andWhere('i.parent = :parent')
            ->setParameter('parent', $comment)

            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ImageComment[] Returns an array of ImageComment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageComment
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
