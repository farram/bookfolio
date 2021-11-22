<?php

namespace App\Factory;

use App\Entity\Social;
use App\Repository\SocialRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static           Social|Proxy findOrCreate(array $attributes)
 * @method static           Social|Proxy random()
 * @method static           Social[]|Proxy[] randomSet(int $number)
 * @method static           Social[]|Proxy[] randomRange(int $min, int $max)
 * @method static           SocialRepository|RepositoryProxy repository()
 * @method Social|Proxy     create($attributes = [])
 * @method Social[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class SocialFactory extends ModelFactory
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
            // ->afterInstantiate(function(Social $social) {})
        ;
    }

    protected static function getClass(): string
    {
        return Social::class;
    }
}
