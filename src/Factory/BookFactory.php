<?php

namespace App\Factory;

use App\Entity\Book;
use App\Repository\BookRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static         Book|Proxy findOrCreate(array $attributes)
 * @method static         Book|Proxy random()
 * @method static         Book[]|Proxy[] randomSet(int $number)
 * @method static         Book[]|Proxy[] randomRange(int $min, int $max)
 * @method static         BookRepository|RepositoryProxy repository()
 * @method Book|Proxy     create($attributes = [])
 * @method Book[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class BookFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        //$design = DesignFactory::random();
        return [
            //'design' => DesignFactory::new(),
            'name' => self::faker()->unique()->domainWord,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Book $book) {})
        ;
    }

    protected static function getClass(): string
    {
        return Book::class;
    }
}
