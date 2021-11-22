<?php

namespace App\Factory;

use App\Entity\EyesColor;
use App\Repository\EyesColorRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static              EyesColor|Proxy findOrCreate(array $attributes)
 * @method static              EyesColor|Proxy random()
 * @method static              EyesColor[]|Proxy[] randomSet(int $number)
 * @method static              EyesColor[]|Proxy[] randomRange(int $min, int $max)
 * @method static              EyesColorRepository|RepositoryProxy repository()
 * @method EyesColor|Proxy     create($attributes = [])
 * @method EyesColor[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class EyesColorFactory extends ModelFactory
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
            // ->afterInstantiate(function(EyesColor $eyesColor) {})
        ;
    }

    protected static function getClass(): string
    {
        return EyesColor::class;
    }

    private static $list = [
        'Noirs',
        'Marrons',
        'Noisettes',
        'Bleus',
        'Gris',
        'Verts',
    ];
}
