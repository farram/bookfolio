<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use App\Service\RandomFlashMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/dashboard/pricing", name="pricing_")
 */
class PricingController extends AbstractController
{
    public function __construct(
        TranslatorInterface $translator,
        RandomFlashMessage $randomFlashMessage,
        Breadcrumbs $breadcrumbs
    ) {
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @Route("/", name="all")
     */
    public function All(PlanRepository $planRepository)
    {
        $this->breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $this->breadcrumbs->addItem($this->translator->trans('Nos formules'));

        $plans = $planRepository->findAll();

        return $this->render('dashboard/pricing/all.html.twig', [
            'plans' => $plans,
            'title' => $this->translator->trans('Formules'),
        ]);
    }
}
