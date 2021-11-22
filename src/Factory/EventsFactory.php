<?php

namespace App\Factory;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static           Events|Proxy findOrCreate(array $attributes)
 * @method static           Events|Proxy random()
 * @method static           Events[]|Proxy[] randomSet(int $number)
 * @method static           Events[]|Proxy[] randomRange(int $min, int $max)
 * @method static           EventsRepository|RepositoryProxy repository()
 * @method Events|Proxy     create($attributes = [])
 * @method Events[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class EventsFactory extends ModelFactory
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
            'type' => $item['type'],
            'text' => $item['text'],
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Events $events) {})
        ;
    }

    protected static function getClass(): string
    {
        return Events::class;
    }

    private static $list = [
        0 => [
            'type' => 'like',
            'text' => 'a aimer votre photo',
        ],
        1 => [
            'type' => 'comment',
            'text' => 'a commenté votre photo',
        ],
        2 => [
            'type' => 'follow',
            'text' => 'a commencé à vous suivre',
        ],
    ];
}
