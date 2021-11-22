<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MigrationController extends AbstractController
{
    /**
     * @Route("migration/generate/uuid", name="migration_uuid")
     */
    public function index(UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $users = $userRepository->findAll();
        //ini_set('max_execution_time', '300');
        //set_time_limit(300);
        foreach ($users as $user) {
            if (empty($user->getUuid())) {
                $update = $userRepository->findOneBy(['id' => $user->getId()]);
                $update->setUuid(Uuid::uuid4());
                $em->persist($update);
                $em->flush();
            }
        }

        return $this->render('migration/index.html.twig', [
            'controller_name' => 'MigrationController',
        ]);
    }
}
