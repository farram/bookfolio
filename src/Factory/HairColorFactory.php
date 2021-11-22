<?php

namespace App\Factory;

use App\Entity\HairColor;
use App\Repository\HairColorRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static              HairColor|Proxy findOrCreate(array $attributes)
 * @method static              HairColor|Proxy random()
 * @method static              HairColor[]|Proxy[] randomSet(int $number)
 * @method static              HairColor[]|Proxy[] randomRange(int $min, int $max)
 * @method static              HairColorRepository|RepositoryProxy repository()
 * @method HairColor|Proxy     create($attributes = [])
 * @method HairColor[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class HairColorFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->unique()->randomElement(self::$list),
            'isActive' => true,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(HairColor $hairColor) {})
        ;
    }

    protected static function getClass(): string
    {
        return HairColor::class;
    }

    private static $list = [
        'Blonds',
        'Bruns',
        'Châtains',
        'Roux',
        'Blancs/Gris',
        'Chauve',
        'Noirs',
    ];
}
