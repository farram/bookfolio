<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/success/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PlanRepository $planRepository, SubscriptionRepository $subscriptionRepository, EntityManagerInterface $em)
    {
        $plan = $planRepository->find($id);
        $subscription = $subscriptionRepository->findOneBy(['plan' => $plan]);

        if (!$plan /*|| ($plan && $subscription->getUser() !== $this->getUser())*/) {
            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'error',
                    'title' => 'Erreur',
                    'message' => 'La commande n\'existe pas',
                ]);

            return $this->redirectToRoute('dashboard_account_subscription');
        }

        $subscription->setStatus(Subscription::STATUS_PAID);
        $em->flush();

        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'success',
                'title' => 'OK',
                'message' => 'La commande payée',
            ]);

        return $this->redirectToRoute('dashboard_account_subscription');
    }
}
