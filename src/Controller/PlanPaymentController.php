<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PaymentRepository;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Service\Mailer;
use App\Service\RandomFlashMessage;
use App\Service\StripeService;
use App\Store\ShoppingCart;
use App\StripeClient;
use App\Subscription\SubscriptionHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/dashboard", name="dashboard_")
 */
class PlanPaymentController extends AbstractController
{
    private $shoppingCart;

    public function __construct(
        Security $security,
        RouterInterface $router,
        TranslatorInterface $translator,
        ShoppingCart $shoppingCart,
        Mailer $mailer,
        RandomFlashMessage $randomFlashMessage
    ) {
        $this->security = $security;
        $this->router = $router;
        $this->translator = $translator;
        $shoppingCart = $shoppingCart;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * @Route("/pricing/plan/{planId}", name="order_add_subscription_to_cart")
     * @IsGranted("ROLE_USER")
     */
    public function orderAddSubscriptionToCart(Breadcrumbs $breadcrumbs, $planId, PlanRepository $planRepository, SubscriptionRepository $subscriptionRepository, StripeService $stripeService, PaymentRepository $paymentRepository, EntityManagerInterface $em)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Nos offres'), $this->get('router')->generate('pricing_all'));

        $plan = $planRepository->findExistingPlan($planId);
        if (!$plan) {
            return $this->redirectToRoute('dashboard_index');
        }

        $breadcrumbs->addItem($this->translator->trans($plan->getPlanName()));
        $intent = $stripeService->getPaymentIntent($plan);

        $stripe = new \Stripe\StripeClient(
            'sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT'
        );
        $price = $stripe->prices->retrieve(
            $planId,
            []
        );

        switch ($price['recurring']['interval']) {
            case 'year':
                $idPrice = $plan->getidPriceApiAnnual();
                break;
            case 'month':
                $idPrice = $plan->getidPriceApi();
                break;
            default:
                break;
        }

        //recurring.interval

        return $this->render('dashboard/pricing/confirmation.html.twig', [
            'clientSecret' => $intent->client_secret,
            'plan' => $plan,
            'price' => $price,
            'idPrice' => $idPrice,
            'stripePublicKey' => $stripeService->getPublicKey(),
            'title' => $this->translator->trans('Offre ' . $plan->getPlanName()),
        ]);
    }

    /**
     * @Route("/create-checkout-session", name="create_checkout_session")
     * @IsGranted("ROLE_USER")
     */
    public function createSession(Request $request): Response
    {
        $body = json_decode($request->getContent());
        \Stripe\Stripe::setApiKey('sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT');
        try {
            $checkoutSession = \Stripe\Checkout\Session::create([
                'success_url' => $this->generateUrl('dashboard_subscription_success', ['planId' => $body->planId], UrlGeneratorInterface::ABSOLUTE_URL) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->generateUrl('dashboard_subscription_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'customer_email' => $this->security->getUser()->getEmail(),
                'line_items' => [[
                    'price' => $body->priceId,
                    'quantity' => 1,
                ]],
            ]);
        } catch (Exception $e) {
        }

        return new Response(json_encode(['sessionId' => $checkoutSession['id']]));
    }

    /**
     * @Route("/account/checkout/success/{planId}", name="subscription_success")
     * @IsGranted("ROLE_USER")
     */
    public function subscriptionSuccess($planId, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper, PlanRepository $planRepository)
    {
        $plan = $planRepository->find($planId);

        \Stripe\Stripe::setApiKey('sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT');
        $id = $_GET['session_id'];
        $checkoutSession = \Stripe\Checkout\Session::retrieve($id);

        $subscription = $subscriptionHelper->findPlan($plan->getIdPriceApi());

        $stripe = new \Stripe\StripeClient(
            'sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT'
        );
        $current_subscription = $stripe->subscriptions->retrieve(
            $checkoutSession->subscription,
            []
        );

        if ($checkoutSession) {
            $subscriptionHelper->addSubscriptionToUser(
                $current_subscription,
                $this->getUser()
            );

            $this->get('session')->getFlashBag()
                ->add('notice', [
                    'type' => 'success',
                    'title' => $this->randomFlashMessage->getTitle(),
                    'message' => $this->translator->trans('Votre abonnement a bien été pris en compte ! Vous allez recevoir un e-mail de confirmation d\'ici quelques minutes.'),
                ]);

            $invoice = $stripe->invoices->retrieve(
                $current_subscription['latest_invoice'],
                []
            );

            // On envoi un mail de confirmation
            $this->mailer->sendEmailPurchaseSuccess($this->getUser(), $subscription, $current_subscription, $invoice);
        } else {
            $stripeClient->createInvoice($this->getUser(), true);
        }

        return $this->redirectToRoute('account_subscription');
    }

    /**
     * @Route("/account/subscription/cancel", name="account_subscription_cancel")
     */
    public function cancelSubscriptionAction(User $user)
    {
        $sub = \Stripe\Subscription::retrieve(
            $user->getSubscription()->getStripeSubscriptionId()
        );

        $sub->cancel([
            'at_period_end' => true,
        ]);
    }

    /**
     * @Route("/account/create-customer-portal-session", name="create_customer_portal_session")
     * @IsGranted("ROLE_USER")
     */
    public function createCustomerPortalSession(Request $request, SubscriptionRepository $subscriptionRepository)
    {
        \Stripe\Stripe::setApiKey('sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT');

        $currentPlan = $subscriptionRepository->findOneBy(['user' => $this->getUser()]);

        $stripe = new \Stripe\StripeClient(
            'sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT'
        );

        $subscriptions = $stripe->subscriptions->retrieve(
            $currentPlan->getStripeSubscriptionId(),
            []
        );

        $session = \Stripe\BillingPortal\Session::create([
            'customer' => $subscriptions->customer,
            'return_url' => $this->generateUrl('account_subscription', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        header('Location: ' . $session->url);
        exit();
    }

    /**
     * @Route("/dashboard/checkout-session", name="checkout_session")
     * @IsGranted("ROLE_USER")
     */
    public function checkoutSession(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT');

        $id = $request->query->get('sessionId');
        $checkout_session = \Stripe\Checkout\Session::retrieve($id);

        return new Response(json_encode($checkout_session));
    }

    /**
     * @Route("/customer-portal", name="customer_portal")
     * @IsGranted("ROLE_USER")
     */
    public function CustomerPortal(Request $request)
    {
        \Stripe\Stripe::setApiKey('sk_test_51ILvF7FekYzBq4jwl6PkckYhFBWpLsWe8Xn60ZcDH3Y3fxkvYqqwDxWsWqwyeeedsRTlYCVGn7MJHO8AskKtMkO200r2Wkn6zT');
        $body = json_decode($request->getContent());

        $checkout_session = \Stripe\Checkout\Session::retrieve($body->sessionId);
        $stripe_customer_id = $checkout_session->customer;
        $return_url = $this->generateUrl('dashboard_', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $session = \Stripe\BillingPortal\Session::create([
            'customer' => $stripe_customer_id,
            'return_url' => $return_url,
        ]);

        return new Response(json_encode(['url' => $session->url]));
    }

    /**
     * @Route("/account/checkout/cancel/", name="subscription_cancel")
     * @IsGranted("ROLE_USER")
     */
    public function handleFailedSubscription()
    {
        $this->get('session')->getFlashBag()
            ->add('notice', [
                'type' => 'error',
                'title' => 'Abonnement annulé',
                'message' => 'Vous avez annulé votre abonnement',
            ]);

        return $this->redirectToRoute('account_subscription');
    }
}
