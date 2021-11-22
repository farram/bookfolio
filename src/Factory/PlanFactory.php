<?php

namespace App\Factory;

use App\Entity\Plan;
use App\Repository\PlanRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static     Plan|Proxy createOne(array $attributes = [])
 * @method static     Plan[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static     Plan|Proxy find($criteria)
 * @method static     Plan|Proxy findOrCreate(array $attributes)
 * @method static     Plan|Proxy first(string $sortedField = 'id')
 * @method static     Plan|Proxy last(string $sortedField = 'id')
 * @method static     Plan|Proxy random(array $attributes = [])
 * @method static     Plan|Proxy randomOrCreate(array $attributes = [])
 * @method static     Plan[]|Proxy[] all()
 * @method static     Plan[]|Proxy[] findBy(array $attributes)
 * @method static     Plan[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static     Plan[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static     PlanRepository|RepositoryProxy repository()
 * @method Plan|Proxy create($attributes = [])
 */
final class PlanFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Plan $plan) {})
        ;
    }

    protected static function getClass(): string
    {
        return Plan::class;
    }
}
