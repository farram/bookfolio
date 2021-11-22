<?php

namespace App\Factory;

use App\Entity\SortListFollow;
use App\Repository\SortListFollowRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static                   SortListFollow|Proxy findOrCreate(array $attributes)
 * @method static                   SortListFollow|Proxy random()
 * @method static                   SortListFollow[]|Proxy[] randomSet(int $number)
 * @method static                   SortListFollow[]|Proxy[] randomRange(int $min, int $max)
 * @method static                   SortListFollowRepository|RepositoryProxy repository()
 * @method SortListFollow|Proxy     create($attributes = [])
 * @method SortListFollow[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class SortListFollowFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $item = self::faker()->unique()->randomElement(self::$list);

        return [
            'sort' => $item['sort'],
            'title' => $item['title'],
            'description' => $item['description'],
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(SortListFollow $sortListFollow) {})
        ;
    }

    protected static function getClass(): string
    {
        return SortListFollow::class;
    }

    private static $list = [
        0 => [
            'sort' => 1,
            'title' => 'createdAt',
            'description' => 'Ajouts récents',
        ],
        1 => [
            'sort' => 2,
            'title' => 'fullname',
            'description' => 'Nom',
        ],
        2 => [
            'sort' => 3,
            'title' => 'lastname',
            'description' => 'Prénom',
        ],
    ];
}
