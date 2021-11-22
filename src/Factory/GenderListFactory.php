<?php

namespace App\Factory;

use App\Entity\GenderList;
use App\Repository\GenderListRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static               GenderList|Proxy findOrCreate(array $attributes)
 * @method static               GenderList|Proxy random()
 * @method static               GenderList[]|Proxy[] randomSet(int $number)
 * @method static               GenderList[]|Proxy[] randomRange(int $min, int $max)
 * @method static               GenderListRepository|RepositoryProxy repository()
 * @method GenderList|Proxy     create($attributes = [])
 * @method GenderList[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class GenderListFactory extends ModelFactory
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
            'id' => $item['id'],
            'title' => $item['title'],
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(GenderList $genderList) {})
        ;
    }

    protected static function getClass(): string
    {
        return GenderList::class;
    }

    private static $list = [
        0 => [
            'id' => 0,
            'title' => 'Homme',
        ],
        1 => [
            'id' => 1,
            'title' => 'Femme',
        ],
        2 => [
            'id' => 2,
            'title' => 'Sans importance',
        ],
    ];
}
