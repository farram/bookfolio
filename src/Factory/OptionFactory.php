<?php

namespace App\Factory;

use App\Entity\Option;
use App\Repository\OptionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static           Option|Proxy findOrCreate(array $attributes)
 * @method static           Option|Proxy random()
 * @method static           Option[]|Proxy[] randomSet(int $number)
 * @method static           Option[]|Proxy[] randomRange(int $min, int $max)
 * @method static           OptionRepository|RepositoryProxy repository()
 * @method Option|Proxy     create($attributes = [])
 * @method Option[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class OptionFactory extends ModelFactory
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
            // ->afterInstantiate(function(Option $option) {})
        ;
    }

    protected static function getClass(): string
    {
        return Option::class;
    }
}
