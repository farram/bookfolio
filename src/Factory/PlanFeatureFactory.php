<?php

namespace App\Factory;

use App\Entity\PlanFeature;
use App\Repository\PlanFeatureRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static            PlanFeature|Proxy createOne(array $attributes = [])
 * @method static            PlanFeature[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static            PlanFeature|Proxy find($criteria)
 * @method static            PlanFeature|Proxy findOrCreate(array $attributes)
 * @method static            PlanFeature|Proxy first(string $sortedField = 'id')
 * @method static            PlanFeature|Proxy last(string $sortedField = 'id')
 * @method static            PlanFeature|Proxy random(array $attributes = [])
 * @method static            PlanFeature|Proxy randomOrCreate(array $attributes = [])
 * @method static            PlanFeature[]|Proxy[] all()
 * @method static            PlanFeature[]|Proxy[] findBy(array $attributes)
 * @method static            PlanFeature[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static            PlanFeature[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static            PlanFeatureRepository|RepositoryProxy repository()
 * @method PlanFeature|Proxy create($attributes = [])
 */
final class PlanFeatureFactory extends ModelFactory
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
            // ->afterInstantiate(function(PlanFeature $planFeature) {})
        ;
    }

    protected static function getClass(): string
    {
        return PlanFeature::class;
    }
}
