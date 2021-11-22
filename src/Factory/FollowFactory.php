<?php

namespace App\Factory;

use App\Entity\Follow;
use App\Repository\FollowRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static           Follow|Proxy findOrCreate(array $attributes)
 * @method static           Follow|Proxy random()
 * @method static           Follow[]|Proxy[] randomSet(int $number)
 * @method static           Follow[]|Proxy[] randomRange(int $min, int $max)
 * @method static           FollowRepository|RepositoryProxy repository()
 * @method Follow|Proxy     create($attributes = [])
 * @method Follow[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class FollowFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'notificationsInterface' => 0,
            'created_at' => new \DateTime('now'),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Follow $follow) {})
        ;
    }

    protected static function getClass(): string
    {
        return Follow::class;
    }
}
