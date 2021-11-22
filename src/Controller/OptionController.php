<?php

namespace App\Controller;

use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/dashboard/option", name="option_")
 */

class OptionController extends AbstractController
{


    /**
     * @Route("/option/darkmode", name="dark_mode")
     */
    public function DarkMode(Request $request, OptionRepository $optionRepository, EntityManagerInterface $em)
    {
        $option = $optionRepository->findOneBy(['user' => $this->getUser()]);
        if ($option) {
            switch ($this->getUser()->getOption()->getDarkMode()) {
                case '1':
                    $value = 0;
                    break;
                case '0':
                    $value = 1;
                    break;
                default:
                    break;
            }
            $option->setDarkMode($value);
            $em->persist($option);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }
}
