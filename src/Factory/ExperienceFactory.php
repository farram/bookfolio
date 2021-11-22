<?php

namespace App\Factory;

use App\Entity\Experience;
use App\Repository\ExperienceRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static               Experience|Proxy findOrCreate(array $attributes)
 * @method static               Experience|Proxy random()
 * @method static               Experience[]|Proxy[] randomSet(int $number)
 * @method static               Experience[]|Proxy[] randomRange(int $min, int $max)
 * @method static               ExperienceRepository|RepositoryProxy repository()
 * @method Experience|Proxy     create($attributes = [])
 * @method Experience[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class ExperienceFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->randomElement(self::$listExperience),
            'isActive' => true,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Experience $experience) {})
        ;
    }

    protected static function getClass(): string
    {
        return Experience::class;
    }

    private static $listExperience = [
        'Première expérience',
        'Amateur',
        'Professionnel',
        'Grande expérience',
    ];
}
