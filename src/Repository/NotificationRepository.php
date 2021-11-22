<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EventsRepository $eventsRepository)
    {
        parent::__construct($registry, Notification::class);
        $this->eventsRepository = $eventsRepository;
    }

    public function findAllOfMe($user, $showComments, $showLikes, $showFollows)
    {
        $ids = [];

        if ('false' == $showComments) {
            $eventComment = $this->eventsRepository->findOneBy(['type' => 'comment']);
            $ids[] = $eventComment->getId();
        }

        if ('false' == $showLikes) {
            $event = $this->eventsRepository->findOneBy(['type' => 'like']);
            $ids[] = $event->getId();
        }

        if ('false' == $showFollows) {
            $event = $this->eventsRepository->findOneBy(['type' => 'follow']);
            $ids[] = $event->getId();
        }

        $qb = $this->createQueryBuilder('n');
        $qb->leftJoin('App:Events', 'e', 'WITH', 'n.event = e.id');
        $qb->where('n.userToNotify = :userToNotify')->setParameter('userToNotify', $user);

        if (!empty($ids)) {
            $qb->andWhere('n.event NOT IN (:ids)')->setParameter('ids', $ids);
        }

        $qb->orderBy('n.createdAt', 'DESC');
        $qb->getQuery();

        return $qb;
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
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
    public function findOneBySomeField($value): ?Notification
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
