<?php

namespace App\Factory;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static            Gallery|Proxy findOrCreate(array $attributes)
 * @method static            Gallery|Proxy random()
 * @method static            Gallery[]|Proxy[] randomSet(int $number)
 * @method static            Gallery[]|Proxy[] randomRange(int $min, int $max)
 * @method static            GalleryRepository|RepositoryProxy repository()
 * @method Gallery|Proxy     create($attributes = [])
 * @method Gallery[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class GalleryFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->name,
            'description' => self::faker()->name,
            'isActive' => true,
            'position' => 1,
            'isProtect' => false,
            'slug' => self::faker()->slug,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Gallery $gallery) {})
        ;
    }

    protected static function getClass(): string
    {
        return Gallery::class;
    }
}
