<?php

namespace App\Factory;

use App\Entity\Statistic;
use App\Repository\StatisticRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static              Statistic|Proxy findOrCreate(array $attributes)
 * @method static              Statistic|Proxy random()
 * @method static              Statistic[]|Proxy[] randomSet(int $number)
 * @method static              Statistic[]|Proxy[] randomRange(int $min, int $max)
 * @method static              StatisticRepository|RepositoryProxy repository()
 * @method Statistic|Proxy     create($attributes = [])
 * @method Statistic[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class StatisticFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $date = new \DateTime(self::faker()->date($format = 'Y-m-d', $max = 'now'));

        return [
            'createdAt' => $date,
            'ipAddress' => self::faker()->ipv4,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Statistic $statistic) {})
        ;
    }

    protected static function getClass(): string
    {
        return Statistic::class;
    }
}
