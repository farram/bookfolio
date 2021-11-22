<?php

namespace App\Factory;

use App\Entity\Physical;
use App\Repository\PhysicalRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static             Physical|Proxy findOrCreate(array $attributes)
 * @method static             Physical|Proxy random()
 * @method static             Physical[]|Proxy[] randomSet(int $number)
 * @method static             Physical[]|Proxy[] randomRange(int $min, int $max)
 * @method static             PhysicalRepository|RepositoryProxy repository()
 * @method Physical|Proxy     create($attributes = [])
 * @method Physical[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class PhysicalFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'sexe' => self::faker()->randomElement($array = [1, 2, 3]),
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Physical $physical) {})
        ;
    }

    protected static function getClass(): string
    {
        return Physical::class;
    }
}
