<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findAllWithSearch(?string $profession, ?string $experience, ?string $location, ?string $origin, ?string $hair, ?string $eyesColor, ?string $gender, $size, $weight, $hip, $confection, $pointure, $sortBy)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.address', 'a')
            ->join('u.physical', 'p')
            ->where('u.isActive = :isActive')
            ->setParameter('isActive', true);

        if ($profession) {
            $qb->andWhere('u.profession = :profession')
                ->setParameter(':profession', $profession);
        }

        if ($experience) {
            $qb->andWhere('u.experience = :experience')
                ->setParameter(':experience', $experience);
        }

        if ($location) {
            $qb->andWhere('a.fullAddress LIKE :location')
                ->setParameter('location', '%' . $location . '%');
        }

        if ($origin) {
            $qb->andWhere('p.ethnicity = :ethnicity')
                ->setParameter('ethnicity', $origin);
        }

        if ($hair) {
            $qb->andWhere('p.hairColor = :hairColor')
                ->setParameter('hairColor', $hair);
        }

        if ($eyesColor) {
            $qb->andWhere('p.eyesColor = :eyesColor')
                ->setParameter('eyesColor', $eyesColor);
        }

        if ($gender) {
            $qb->andWhere('p.gender = :gender')
                ->setParameter('gender', $gender);
        }

        if ($size) {
            $expressionBuilder = $qb->expr();
            $qb->andWhere(
                $expressionBuilder->orX(
                    $expressionBuilder->between('p.size', $size[0], $size[1])
                )
            );
        }

        if ($weight) {
            $expressionBuilder = $qb->expr();
            $qb->andWhere(
                $expressionBuilder->orX(
                    $expressionBuilder->between('p.weight', $weight[0], $weight[1])
                )
            );
        }

        if ($hip) {
            $expressionBuilder = $qb->expr();
            $qb->andWhere(
                $expressionBuilder->orX(
                    $expressionBuilder->between('p.hip', $hip[0], $hip[1])
                )
            );
        }

        if ($confection) {
            $expressionBuilder = $qb->expr();
            $qb->andWhere(
                $expressionBuilder->orX(
                    $expressionBuilder->between('p.confection', $confection[0], $confection[1])
                )
            );
        }

        if ($pointure) {
            $expressionBuilder = $qb->expr();
            $qb->andWhere(
                $expressionBuilder->orX(
                    $expressionBuilder->between('p.pointure', $pointure[0], $pointure[1])
                )
            );
        }

        if ('updatedAt' == $sortBy) {
            $qb->orderBy('u.' . $sortBy . '', 'DESC');
        } else {
            $qb->orderBy('u.' . $sortBy . '', 'ASC');
        }

        return $qb->getQuery();
    }

    public function findNewUsers($limit, $user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :user')
            ->setParameter('user', $user)

            ->andWhere('u.isActive = :isActive')
            ->setParameter('isActive', true)

            ->andWhere('u.isDemo = :isDemo')
            ->setParameter('isDemo', false)

            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)

            ->getQuery()
            ->getResult();
    }

    public function findNewUsersForFront($limit)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findUsersForHome($limit)
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = :isActive')
            ->setParameter('isActive', true)
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findFollowUsers($limit, $user)
    {
        // Récupération de la liste des follows
        $follows = $this->createQueryBuilder('u')
            ->leftJoin('App:Follow', 'f', 'WITH', 'u.id = f.friend')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        // Récupération de la liste à ne pas suggérer
        $notSuggested = $this->createQueryBuilder('u')
            ->leftJoin('App:UnsuggestBook', 'un', 'WITH', 'u.id = un.book')
            ->where('un.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $qb = $this->createQueryBuilder('u');
        $qb->innerJoin('u.images', 'i');
        $qb->where('u.id != :user');
        $qb->setParameter('user', $user);
        if ($follows) {
            $qb->andWhere('u.id NOT IN (:ids)');
            $qb->setParameter('ids', $follows);
        }
        if ($notSuggested) {
            $qb->andWhere('u.id NOT IN (:unsuggestBooks)');
            $qb->setParameter('unsuggestBooks', $notSuggested);
        }
        $qb->andWhere('u.isVerified = :verified')->setParameter('verified', 1);
        $qb->andWhere('u.isActive = :active')->setParameter('active', 1);
        $qb->andWhere('u.isDemo != :demo')->setParameter('demo', 1);
        $qb->distinct('u.id');
        $qb->orderBy('u.createdAt', 'DESC');
        $qb->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

    public function findSuggestBooks($user, ?string $profession, ?string $experience, ?string $sexe, $location)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->andWhere("u.about != ''");
        $qb->andWhere('u.about IS NOT NULL');

        if ($profession) {
            $qb->andWhere('u.profession = :profession')
                ->setParameter(':profession', $profession);
        }

        if ($experience) {
            $qb->andWhere('u.experience = :experience')
                ->setParameter(':experience', $experience);
        }

        if ($sexe) {
            $qb->leftJoin('App:Physical', 'p', 'WITH', 'u.id = p.user');
            $qb->andWhere('p.sexe = :sexe')->setParameter(':sexe', $sexe);
        }

        if ($location) {
            $qb->join('u.address', 'a');
            $qb->andWhere('a.fullAddress LIKE :location')
                ->setParameter('location', '%' . $location . '%');
        }

        $qb->andWhere('u.id != :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('u.updatedAt', 'DESC');

        return $qb->getQuery();
    }

    public function findUsers($search)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username LIKE :username')->setParameter('username', '%' . $search . '%')
            ->orWhere('u.lastname LIKE :lastname')->setParameter('lastname', '%' . $search . '%')
            ->orWhere('u.firstname LIKE :firstname')->setParameter('firstname', '%' . $search . '%')
            ->andWhere('u.isVerified = :verified')->setParameter('verified', 1)
            ->andWhere('u.isActive = :active')->setParameter('active', 1)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery();
    }

    public function findUsersQuery($search)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')->setParameter('username', $search)
            ->orWhere('u.lastname = :lastname')->setParameter('lastname', $search)
            ->orWhere('u.firstname = :firstname')->setParameter('firstname', $search)
            ->andWhere('u.isVerified = :verified')->setParameter('verified', 1)
            ->andWhere('u.isActive = :active')->setParameter('active', 1)
            ->orderBy('u.createdAt', 'DESC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByActive($limit)
    {
        /*SELECT *
        FROM user
        WHERE id IN (
        SELECT user_id
        FROM media_pro
        WHERE status=:status
        GROUP BY user_id
        HAVING count(user_id) > '. Yii::$app->params['nb_mediasByweek'].'
        )
        AND status=:status
        AND email != :email
        AND ((about IS NOT NULL) OR (about !=""))
        AND ((register_social IS NULL) OR (register_social=:social))
        ORDER BY RAND()
*/

        $images = $this->createQueryBuilder('u')
            ->leftJoin('App:Images', 'i', 'WITH', 'u.id = i.user')
            ->where('i.isVisible = :isVisible')
            ->setParameter('isVisible', 1)
            ->groupBy('i.user')
            ->having('COUNT(i.user) >= 1')
            ->getQuery()
            ->getResult();

        return $this->createQueryBuilder('u')
            ->where('u.isActive = :isActive')
            ->setParameter('isActive', true)
            ->andWhere('u.id IN (:images)')
            ->setParameter('images', $images)
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findSignupThisYear()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id) AS count, u.createdAt, MONTH(u.createdAt) AS month')
            ->andWhere('u.isActive = :isActive')
            ->setParameter('isActive', true)

            ->andwhere('YEAR(u.createdAt) = :year')
            ->setParameter('year', date('Y'))

            ->groupBy('month')
            ->orderBy('u.createdAt')

            ->getQuery()
            ->getResult();
    }

    public function findSignupThisMonth()
    {
        // SELECT DATE(created_date), count(*) AS count, created_date
        // FROM user
        // WHERE status = '1' AND MONTH(created_date) = ".$thisMonth." AND YEAR(created_date) = ".$thisYear."
        // GROUP BY date(created_date)

        return $this->createQueryBuilder('u')
            ->select('DATE(u.createdAt) as date, count(u.id) AS count, u.createdAt')
            ->andWhere('u.isActive = :isActive')
            ->setParameter('isActive', true)

            ->andwhere('MONTH(u.createdAt) = :month')
            ->setParameter('month', date('m'))

            ->andwhere('YEAR(u.createdAt) = :year')
            ->setParameter('year', date('Y') - 1)

            ->groupBy('date')

            ->getQuery()
            ->getResult();
    }

    public function findTypeBook()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id) AS count, p.title, p.color')
            ->join('u.profession', 'p')
            ->andWhere('u.isActive = :isActive')
            ->setParameter('isActive', true)
            ->groupBy('u.profession')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOrCreateFromGoogleOauth(ResourceOwnerInterface $owner): User
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $owner->getEmail())
            ->getQuery()
            ->getOneOrNullResult();
        if ($user) {
            return $user;
        }
        //$user = (new User());
    }
}
