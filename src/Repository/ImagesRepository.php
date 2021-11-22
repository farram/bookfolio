<?php

namespace App\Repository;

use App\Entity\Images;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Images|null find($id, $lockMode = null, $lockVersion = null)
 * @method Images|null findOneBy(array $criteria, array $orderBy = null)
 * @method Images[]    findAll()
 * @method Images[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Images::class);
    }

    // Récupération de l'image la plus récente de la galerie
    public function findLastFromGallery($id)
    {
        return $this->createQueryBuilder('img')
            ->andWhere('img.gallery = :val')
            ->setParameter('val', $id)
            ->orderBy('img.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMinPosition($galleryid)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('MIN(i.position)')
            ->andWhere('i.gallery >= :galleryid')
            ->setParameter('galleryid', $galleryid);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllImageFromGallery($book, $gallery)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.gallery', 'g')

            ->where('g.isActive = :active')
            ->setParameter('active', true)

            ->andWhere('i.gallery = :gal')
            ->setParameter('gal', $gallery)

            ->andWhere('i.user = :user')
            ->setParameter('user', $book->getUser())

            ->andWhere('i.isVisible = :visible')
            ->setParameter('visible', true)

            ->andWhere('i.isGallery = :gallery')
            ->setParameter('gallery', false)

            ->orderBy('i.position', 'DESC')
            ->getQuery();
    }

    public function findAllImagePublic($book)
    {
        $fields = ['i.id', 'i.isVisible', 'i.position'];

        return $this->createQueryBuilder('i')

            ->leftJoin('i.gallery', 'g')

            ->where('g.isProtect IS NULL')

            ->andWhere('g.isActive = :active')
            ->setParameter('active', true)

            ->andWhere('i.user = :user')
            ->setParameter('user', $book->getUser())

            ->andWhere('i.isVisible = :visible')
            ->setParameter('visible', true)

            ->andWhere('i.isGallery = :gallery')
            ->setParameter('gallery', false)

            ->orderBy('i.position', 'asc')
            ->getQuery();
        //->getResult();
    }

    public function findMyAllImage($user)
    {
        return $this->createQueryBuilder('i')
            ->where('i.user = :user')
            ->setParameter('user', $user)

            ->orderBy('i.position', 'asc')
            ->getQuery();
    }

    public function findImagesFromGallery($gallery, $limit)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.gallery', 'g')
            ->andWhere('i.gallery = :gallery')
            ->setParameter('gallery', $gallery)
            ->setMaxResults($limit)
            ->orderBy('i.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findImagesLimit($book, $limit)
    {
        return $this->createQueryBuilder('i')
            ->where('i.user = :user')
            ->setParameter('user', $book->getUser())
            ->andWhere('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)
            ->setMaxResults($limit)
            ->orderBy('i.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findMyFeed($me)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('App:Follow', 'f', 'WITH', 'i.user = f.friend')
            ->where('f.user = :me')
            ->setParameter('me', $me)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery();
    }

    public function findNewFresh($user, $displayBy)
    {
        switch ($displayBy) {
            case 'following':
                return $this->fromFollowing($user);
                break;
            case 'all':
                return $this->fromAll();
                break;
            case 'popular':
                return $this->fromPopular();
                break;
        }

        // Si l'utilisateur connecté follow des books
        // if (count($this->findMyFeed($user)->getResult()) > 0) {
    }

    public function fromFollowing($user)
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('App:Follow', 'f', 'WITH', 'i.user = f.friend')
            ->leftJoin('App:Gallery', 'g', 'WITH', 'i.gallery = g.id')
            ->andWhere('g.passwordHash is NULL')

            ->leftJoin('App:User', 'u', 'WITH', 'i.user = u.id')
            ->andWhere('u.isDemo is NULL')

            ->where('f.user = :me')
            ->setParameter('me', $user)

            ->andWhere('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)

            ->andWhere('i.isNSFW = :isNSFW')
            ->setParameter('isNSFW', false)

            ->andWhere('i.isGallery = :isGallery')
            ->setParameter('isGallery', false)

            ->andWhere('i.isHome = :isHome')
            ->setParameter('isHome', true)

            ->orderBy('i.createdAt', 'DESC')
            ->getQuery();
    }

    public function fromAll()
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('App:Gallery', 'g', 'WITH', 'i.gallery = g.id')
            ->andWhere('g.passwordHash is NULL')

            ->andWhere('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)

            ->andWhere('i.isNSFW = :isNSFW')
            ->setParameter('isNSFW', false)

            ->andWhere('i.isGallery = :isGallery')
            ->setParameter('isGallery', false)

            ->andWhere('i.isHome = :isHome')
            ->setParameter('isHome', true)

            ->orderBy('i.createdAt', 'DESC')
            ->getQuery();
    }

    public function fromPopular()
    {
        return $this->createQueryBuilder('i')

            ->leftJoin('App:User', 'u', 'WITH', 'i.user = u.id')
            ->leftJoin('App:Gallery', 'g', 'WITH', 'i.gallery = g.id')
            ->andWhere('g.passwordHash is NULL')

            ->andWhere('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)

            ->andWhere('i.isNSFW = :isNSFW')
            ->setParameter('isNSFW', false)

            ->andWhere('i.isGallery = :isGallery')
            ->setParameter('isGallery', false)

            ->andWhere('i.isHome = :isHome')
            ->setParameter('isHome', true)

            ->andWhere('u.isActive = :isActive')
            ->setParameter('isActive', true)

            ->orderBy('i.countView', 'DESC')
            ->getQuery();
    }

    public function findCountImagesUploadThisMonth($user, $month)
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i)')
            ->andWhere('i.user = :user')
            ->setParameter('user', $user)

            ->andWhere('i.createdAt >= :date')
            ->setParameter('date', $month->format('Y-m-d 00:00:00'))

            ->getQuery()
            ->getSingleScalarResult();
        //->count();
    }

    public function findPublicImages($user)
    {
        return $this->createQueryBuilder('i')

            ->where('i.user = :user')
            ->setParameter('user', $user)

            ->andWhere('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)

            ->andWhere('i.isGallery = :isGallery')
            ->setParameter('isGallery', false)

            ->getQuery()
            ->getResult();
    }

    public function findFreshImagesLimit($limit)
    {
        return $this->createQueryBuilder('i')
            ->where('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)
            ->setMaxResults($limit)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUserLimit($user, $limit)
    {
        return $this->createQueryBuilder('i')
            ->where('i.user = :user')
            ->setParameter('user', $user)
            ->andWhere('i.isVisible = :isVisible')
            ->setParameter('isVisible', true)
            ->andWhere('i.isNSFW = :isNSFW')
            ->setParameter('isNSFW', false)
            ->andWhere('i.isGallery = :isGallery')
            ->setParameter('isGallery', false)
            ->andWhere('i.isHome = :isHome')
            ->setParameter('isHome', true)
            ->setMaxResults($limit)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
