<?php

namespace App\DataFixtures;

use App\Factory\AddressFactory;
use App\Factory\BookFactory;
use App\Factory\DesignFactory;
use App\Factory\EthnicityFactory;
use App\Factory\EventsFactory;
use App\Factory\ExperienceFactory;
use App\Factory\EyesColorFactory;
use App\Factory\FollowFactory;
use App\Factory\GalleryFactory;
use App\Factory\GenderListFactory;
use App\Factory\GuestbookFactory;
use App\Factory\HairColorFactory;
use App\Factory\OptionFactory;
use App\Factory\PhysicalFactory;
use App\Factory\ProfessionFactory;
use App\Factory\SocialFactory;
use App\Factory\SortListFollowFactory;
use App\Factory\StatisticFactory;
use App\Factory\StylePhotosFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création de la liste des designs
        $design = DesignFactory::new()->many(8)->create();

        SortListFollowFactory::new()->many(3)->create();
        EventsFactory::new()->many(3)->create();
        GenderListFactory::new()->many(3)->create();
        HairColorFactory::new()->many(7)->create();
        StylePhotosFactory::new()->many(23)->create();
        EthnicityFactory::new()->many(8)->create();
        EyesColorFactory::new()->many(5)->create();

        // Création de la liste des profession
        $profession = ProfessionFactory::new()->many(20)->create();

        // Création de la liste des expériences basées sur la profession
        $experience = ExperienceFactory::new()->create(['profession' => ProfessionFactory::random()]);

        // Création des utilisateurs
        $nb = 200;
        $users = UserFactory::new()->many($nb)->create(['profession' => ProfessionFactory::random(), 'experience' => $experience]);

        foreach ($users as $user) {
            AddressFactory::new()->create(['user' => $user]); //-> Adresse
            PhysicalFactory::new()->create(['user' => $user]); //-> Critères physique
            OptionFactory::new()->create(['user' => $user]); //-> Les options
            SocialFactory::new()->create(['user' => $user]); //-> Réseaux sociaux
            StatisticFactory::new()->many(2)->create(['user' => $user]); //-> Statistiques
            GuestbookFactory::new()->many(2)->create(['user' => $user]); //-> Livre d'or
            GalleryFactory::new()->many(2)->create(['user' => $user]); //-> Création des galeries
            BookFactory::new()->create(['user' => $user, 'design' => DesignFactory::random()]); //-> Message d'erreur "Notice: Array to string conversion"
            //FollowFactory::new()->many(5)->create(['user' => $user, 'friend' => $user]); //-> Follow
            //
        }
    }
}
