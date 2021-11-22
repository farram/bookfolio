<?php

namespace App\Factory;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static            Address|Proxy findOrCreate(array $attributes)
 * @method static            Address|Proxy random()
 * @method static            Address[]|Proxy[] randomSet(int $number)
 * @method static            Address[]|Proxy[] randomRange(int $min, int $max)
 * @method static            AddressRepository|RepositoryProxy repository()
 * @method Address|Proxy     create($attributes = [])
 * @method Address[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class AddressFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'fullAddress' => self::faker()->city.', '.self::faker()->country,
            'route' => self::faker()->streetAddress,
            'locality' => self::faker()->city,
            'adminstrativeArea' => self::faker()->streetName,
            'country' => self::faker()->country,
            'postalCode' => self::faker()->postcode,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Address $address) {})
        ;
    }

    protected static function getClass(): string
    {
        return Address::class;
    }
}
