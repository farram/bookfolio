<?php

namespace App\Factory;

use App\Entity\Design;
use App\Repository\DesignRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static           Design|Proxy findOrCreate(array $attributes)
 * @method static           Design|Proxy random()
 * @method static           Design[]|Proxy[] randomSet(int $number)
 * @method static           Design[]|Proxy[] randomRange(int $min, int $max)
 * @method static           DesignRepository|RepositoryProxy repository()
 * @method Design|Proxy     create($attributes = [])
 * @method Design[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class DesignFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        //$title = self::faker()->randomElement(self::$list);
        //$slug = self::faker()->slug($title);

        return [
            'title' => self::faker()->unique()->randomElement(self::$list),
            'description' => self::faker()->sentence(20),
            'image' => '',
            'isActive' => true,
            'isCustom' => false,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Design $design) {})
        ;
    }

    protected static function getClass(): string
    {
        return Design::class;
    }

    private static $list = [
        'Tile Light',
        'Tile Dark',
        'Wide Dark',
        'Wide Light',
        'Tile Full Light',
        'Tile Full Dark',
        'Alba Light',
        'Alba Dark',
        'Illo Dark',
        'Illo Light',
        'Mosaic Light',
        'Mosaic Dark',
        'Folio Light',
        'Folio Dark',
        'Kool Light',
        'Kool Dark',
        'Big Gap Light',
        'Big Gap Dark',
    ];
}
