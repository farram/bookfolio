<?php

namespace App\Factory;

use App\Entity\Profession;
use App\Repository\ProfessionRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static               Profession|Proxy findOrCreate(array $attributes)
 * @method static               Profession|Proxy random()
 * @method static               Profession[]|Proxy[] randomSet(int $number)
 * @method static               Profession[]|Proxy[] randomRange(int $min, int $max)
 * @method static               ProfessionRepository|RepositoryProxy repository()
 * @method Profession|Proxy     create($attributes = [])
 * @method Profession[]|Proxy[] createMany(int $number, $attributes = [])
 */
final class ProfessionFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->unique()->randomElement(self::$listJob),
            'description' => self::faker()->jobTitle,
            //'slug'=> self::faker()->slug,
            'isActive' => true,
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Profession $profession) {})
        ;
    }

    protected static function getClass(): string
    {
        return Profession::class;
    }

    private static $listJob = [
        'Modèle',
        'Photographe',
        'Maquilleur / Maquilleuse',
        'Comédien / Comédienne',
        'Danseur / Danseuse',
        'Musicien / Musicienne',
        'Vidéaste',
        'Styliste',
        'Designer graphique',
        'Auteur',
        'Artisan',
        'Agence',
        'Chanteur / Chanteuse',
        'Coiffeur / Coiffeuse',
        'Chargé(e) de communication',
        'Artiste peintre',
        'Artiste sculpteur',
        'Maître dart',
        'Danseuse et modèle',
        'Danseur et modèle',
    ];
}
