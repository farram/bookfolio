<?php

namespace App\Factory;

use App\Entity\Ethnicity;
use App\Repository\EthnicityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static              Ethnicity|Proxy findOrCreate(array $attributes)
 * @method static              Ethnicity|Proxy random()
 * @method static              Ethnicity[]|Proxy[] randomSet(int $number)
 * @method static              Ethnicity[]|Proxy[] randomRange(int $min, int $max)
 * @method static              EthnicityRepository|RepositoryProxy repository()
 * @method Ethnicity|Proxy     create($attributes = [])
 * @method Ethnicity[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class EthnicityFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->unique()->randomElement(self::$list),
            'isActive' => true,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Ethnicity $ethnicity) {})
        ;
    }

    protected static function getClass(): string
    {
        return Ethnicity::class;
    }

    private static $list = [
        'Européen(e)',
        'Asiatique',
        'Latino-américain',
        'Arabe/Moyen-Orient',
        'Indien',
        'Métissé',
        'Africaine',
        'Autres',
    ];
}
