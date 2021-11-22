<?php

namespace App\Repository;

use App\Entity\Design;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Design|null find($id, $lockMode = null, $lockVersion = null)
 * @method Design|null findOneBy(array $criteria, array $orderBy = null)
 * @method Design[]    findAll()
 * @method Design[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DesignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Design::class);
    }

    public function findByActive()
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('d.position', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllActiveForVuejs($type)
    {
        $query = $this->createQueryBuilder('d');
        $query->andWhere('d.isActive = :isActive');
        $query->setParameter('isActive', true);

        if ($type) {
            $query->leftJoin('d.plan', 'p');
            $query->andWhere('p.id = :type');
            $query->setParameter('type', $type);
        }

        $query->orderBy('d.position', 'DESC');
        $query->getQuery();

        return $query;
    }
}
