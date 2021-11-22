<?php

namespace App\Controller;

use App\Service\RandomFlashMessage;
use App\StripeClient;
use App\Subscription\SubscriptionHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriptionController extends AbstractController
{
    public function __construct(TranslatorInterface $translator, Security $security, RandomFlashMessage $randomFlashMessage)
    {
        $this->translator = $translator;
        $this->security = $security;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * @Route("/invoice/create")
     */
    public function create(NotifierInterface $notifier)
    {
        $notification = (new Notification('New Invoice', ['email']))
            ->content('You got a new invoice for 15 EUR.')
            ->importance(Notification::IMPORTANCE_HIGH);

        $user = $this->getUser();
        $recipient = new AdminRecipient(
            $user->getEmail(),
        );
        $notifier->send($notification, $recipient);
    }

    /**
     * @Route("/profile/subscription/cancel", name="account_subscription_cancel", methods={"POST"})
     */
    public function cancelSubscriptionAction(StripeClient $stripeClient)
    {
        $stripeSubscription = $stripeClient->cancelSubscription($this->getUser());

        $subscription = $this->getUser()->getSubscription();

        if ('canceled' == $stripeSubscription->status) {
            // the subscription was cancelled immediately
            $subscription->cancel();
        } else {
            $subscription->deactivateSubscription();
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($subscription);
        $em->flush();

        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'success',
                'title' => $this->translator->trans('Abonnement annulé :('),
                'message' => $this->translator->trans('Votre demande d\'annulation a bien été prise en compte. Vous ne serez pas facturé à la prochaine échéance.'),
            ]);

        return $this->redirectToRoute('account_subscription');
    }

    /**
     * @Route("/profile/subscription/reactivate", name="account_subscription_reactivate")
     */
    public function reactivateSubscriptionAction(StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper)
    {
        $stripeSubscription = $stripeClient->reactivateSubscription($this->getUser());

        $subscriptionHelper
            ->addSubscriptionToUser($stripeSubscription, $this->getUser());

        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'success',
                'title' => $this->randomFlashMessage->getTitle(),
                'message' => $this->translator->trans('La réactivation de votre abonnement a bien été prise en compte. Nous nous occupons du reste :)'),
            ]);

        return $this->redirectToRoute('account_subscription');
    }

    /**
     * @Route("/profile/plan/change/preview/{planId}", name="account_preview_plan_change")
     */
    public function previewPlanChangeAction($planId, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper)
    {
        $plan = $subscriptionHelper
            ->findPlan($planId);

        $stripeInvoice = $stripeClient
            ->getUpcomingInvoiceForChangedSubscription(
                $this->getUser(),
                $plan
            );

        $currentUserPlan = $subscriptionHelper
            ->findPlan($this->getUser()->getSubscription()->getStripePlanId());

        $total = $stripeInvoice->amount_due;

        $subscription = $subscriptionHelper->findPlan($currentUserPlan->getPlanId());
        $stripeSubscription = $stripeClient->createSubscription(
            $this->getUser(),
            $subscription
        );

        $subscriptionHelper->addSubscriptionToUser(
            $stripeSubscription,
            $this->getUser()
        );

        // contains the pro-rations
        // *plus* - if the duration matches - next cycle's amount
        if ($plan->getDuration() == $currentUserPlan->getDuration()) {
            // subtract plan price to *remove* next the next cycle's total
            $total -= $plan->getPrice() * 100;
        }

        return new JsonResponse(['total' => $total / 100]);
    }

    /**
     * @Route("/profile/plan/change/execute/{planId}", name="account_execute_plan_change", methods={"POST"})
     */
    public function changePlanAction($planId, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper)
    {
        $plan = $subscriptionHelper
            ->findPlan($planId);

        try {
            $stripeSubscription = $stripeClient->changePlan($this->getUser(), $plan);
        } catch (\Stripe\Error\Card $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 400);
        }

        // causes the planId to be updated on the user's subscription
        $subscriptionHelper->addSubscriptionToUser($stripeSubscription, $this->getUser());

        return new Response(null, 204);
    }

    /**
     * @Route("/profile/invoices/{invoiceId}", name="account_invoice_show")
     */
    public function showInvoiceAction($invoiceId, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper)
    {
        $stripeInvoice = $stripeClient
            ->findInvoice($invoiceId);

        return $this->render('dashboard/account/billing/invoice.html.twig', [
            'invoice' => $stripeInvoice,
        ]);
    }

    /**
     * @Route("/profile/card/update", name="account_update_credit_card", methods={"POST"})
     */
    public function updateCreditCardAction(Request $request, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper)
    {
        $token = $request->request->get('stripeToken');
        $user = $this->getUser();

        try {
            $stripeCustomer = $stripeClient->updateCustomerCard(
                $user,
                $token
            );
        } catch (\Stripe\Error\Card $e) {
            $error = 'There was a problem charging your card: '.$e->getMessage();

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'error',
                    'title' => 'Problem',
                    'message' => $error,
                ]);

            return $this->redirectToRoute('account_subscription');
        }

        // save card details!
        $subscriptionHelper
            ->updateCardDetails($user, $stripeCustomer);
        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'success',
                'title' => 'Card updated!',
                'message' => 'message',
            ]);

        return $this->redirectToRoute('account_subscription');
    }
}
