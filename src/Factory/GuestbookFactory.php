<?php

namespace App\Factory;

use App\Entity\Guestbook;
use App\Repository\GuestbookRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static              Guestbook|Proxy findOrCreate(array $attributes)
 * @method static              Guestbook|Proxy random()
 * @method static              Guestbook[]|Proxy[] randomSet(int $number)
 * @method static              Guestbook[]|Proxy[] randomRange(int $min, int $max)
 * @method static              GuestbookRepository|RepositoryProxy repository()
 * @method Guestbook|Proxy     create($attributes = [])
 * @method Guestbook[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class GuestbookFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'author' => self::faker()->name,
            'location' => self::faker()->city,
            'website' => self::faker()->url,
            'email' => self::faker()->email,
            'content' => self::faker()->paragraph,
            'createdAt' => self::faker()->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null),
            'isActive' => self::faker()->randomElement(self::$list),
            'ipAddress' => self::faker()->ipv4,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Guestbook $guestbook) {})
        ;
    }

    protected static function getClass(): string
    {
        return Guestbook::class;
    }

    private static $list = [0, 1];
}
