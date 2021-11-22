<?php

namespace App\Factory;

use App\Entity\StylePhotos;
use App\Repository\StylePhotosRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static                StylePhotos|Proxy findOrCreate(array $attributes)
 * @method static                StylePhotos|Proxy random()
 * @method static                StylePhotos[]|Proxy[] randomSet(int $number)
 * @method static                StylePhotos[]|Proxy[] randomRange(int $min, int $max)
 * @method static                StylePhotosRepository|RepositoryProxy repository()
 * @method StylePhotos|Proxy     create($attributes = [])
 * @method StylePhotos[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class StylePhotosFactory extends ModelFactory
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
            // ->afterInstantiate(function(StylePhotos $stylePhotos) {})
        ;
    }

    protected static function getClass(): string
    {
        return StylePhotos::class;
    }

    private static $list = [
        'Abstrait',
        'Mariage',
        'Nature',
        'Sport',
        'Portrait',
        'Nu artistique',
        'Mode',
        'Détail',
        'Charme',
        'Glamour',
        'Lingerie',
        'Photo d\'entreprise',
        'Artistiques',
        'Animalière',
        'Aérienne',
        'Sous-marine',
        'Culinaire',
        'Architecture',
        'Lightpainting',
        'Reportage',
        'fantastiques',
        'Photojournalisme',
        'Urbain',
    ];
}
