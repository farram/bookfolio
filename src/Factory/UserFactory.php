<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use function Zenstruck\Foundry\repository;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static         User|Proxy findOrCreate(array $attributes)
 * @method static         User|Proxy random()
 * @method static         User[]|Proxy[] randomSet(int $number)
 * @method static         User[]|Proxy[] randomRange(int $min, int $max)
 * @method static         UserRepository|RepositoryProxy repository()
 * @method User|Proxy     create($attributes = [])
 * @method User[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    /**
     * @param string $uploadDir
     */
    private $passwordEncoder;
    private $container;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactoryInterface)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordHasherFactoryInterface;
        $this->container =  new ContainerBuilder();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $targetPath = $this->container->getParameter('kernel.project_dir') . '/public/assets/uploads/avatar';

        return [
            'email' => self::faker()->unique()->safeEmail,
            'password' => 'testtest',
            'uuid' => Uuid::uuid4(),
            'is_verified' => true,
            'access_token' => false,
            'created_at' => new \DateTime('now'),
            'updated_at' => new \DateTime('now'),
            'is_active' => true,
            'username' => self::faker()->unique()->userName,
            'lastname' => self::faker()->unique()->lastName,
            'firstname' => self::faker()->unique()->firstName,
            'thumbnail' => self::faker()->unique()->file(__DIR__ . '/images/', $targetPath . '/', false), // '13b73edae8443990be1aa8f1a483bc27.jpg'
        ];
    }

    public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return $this->uploadDir;
    }

    private static $profileImages = [
        'user-1.jpg', 'user-2.jpg', 'user-3.jpg',
    ];

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (User $user) {
                $user->setPassword($this->passwordEncoder->getPasswordHasher($user, $user->getPassword()));
            });
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
