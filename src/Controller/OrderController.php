<?php

namespace App\Controller;

use App\Entity\User;
use App\StripeClient;
use App\Entity\Product;
use App\Store\ShoppingCart;
use App\Service\RandomFlashMessage;
use App\Subscription\SubscriptionHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $shoppingCart;
    private $stripeClient;

    public function __construct(
        ShoppingCart $shoppingCart,
        StripeClient $stripeClient,
        TranslatorInterface $translator,
        RandomFlashMessage $randomFlashMessage
    ) {
        $shoppingCart = $shoppingCart;
        $stripeClient = $stripeClient;
        $this->translator = $translator;
        $this->randomFlashMessage = $randomFlashMessage;
    }

    /**
     * @Route("dashboard/cart/product/{slug}", name="order_add_product_to_cart")
     */
    public function addProductToCartAction(Product $product)
    {
        $this->shoppingCart->addProduct($product);
        $this->addFlash('success', 'Product added!');

        return $this->redirectToRoute('order_checkout');
    }

    /**
     * @Route("dashboard/cart/subscription/{planId}", name="order_add_subscription_to_cart")
     */
    public function addSubscriptionToCartAction($planId, SubscriptionHelper $subscriptionHelper, ShoppingCart $shoppingCart)
    {
        $plan = $subscriptionHelper->findPlan($planId);

        if (!$plan) {
            throw $this->createNotFoundException('Bad plan id!');
        }

        $shoppingCart->addSubscription($planId);

        return $this->redirectToRoute('order_checkout');
    }

    /**
     * @Route("dashboard/account/checkout", name="order_checkout")
     * @Security("is_granted('ROLE_USER')")
     */
    public function checkoutAction(Request $request, ShoppingCart $shoppingCart, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper, Breadcrumbs $breadcrumbs)
    {
        $breadcrumbs->addItem($this->translator->trans('Tableau de bord'), $this->get('router')->generate('dashboard_index'));
        $breadcrumbs->addItem($this->translator->trans('Offres'), $this->get('router')->generate('pricing_all'));
        $breadcrumbs->addItem('Finalisation de la commande');

        $products = $shoppingCart->getProducts();

        $error = false;
        if ($request->isMethod('POST')) {
            $token = $request->request->get('stripeToken');

            try {
                $this->chargeCustomer($token, $shoppingCart, $stripeClient, $subscriptionHelper);
            } catch (\Stripe\Error\Card $e) {
                $error = 'There was a problem charging your card: ' . $e->getMessage();
            }

            if (!$error) {
                $shoppingCart->emptyCart();

                $this->get('session')->getFlashBag()
                    ->add('notice', [
                        'type' => 'success',
                        'title' => 'Order Complete! Yay!',
                        'message' => 'message',
                    ]);

                return $this->redirectToRoute('account_subscription');
            }
        }

        return $this->render('dashboard/order/checkout.html.twig', [
            'products' => $products,
            'cart' => $shoppingCart,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'error' => $error,
        ]);
    }

    /**
     * @Route("dashboard/checkout/coupon", name="order_add_coupon")
     */
    public function addCouponAction(Request $request)
    {
        $code = $request->request->get('code');

        if (!$code) {
            $this->addFlash('error', 'Missing coupon code!');

            return $this->redirectToRoute('order_checkout');
        }

        try {
            $stripeCoupon = $this->get('stripe_client')
                ->findCoupon($code);
        } catch (\Stripe\Error\InvalidRequest $e) {
            $this->addFlash('error', 'Invalid coupon code!');

            return $this->redirectToRoute('order_checkout');
        }

        if (!$stripeCoupon->valid) {
            $this->addFlash('error', 'Coupon expired');

            return $this->redirectToRoute('order_checkout');
        }

        $this->shoppingCart->setCouponCode($code, $stripeCoupon->amount_off / 100);

        $this->addFlash('success', 'Coupon applied!');

        return $this->redirectToRoute('order_checkout');
    }

    /**
     * @param $token
     *
     * @throws \Stripe\Error\Card
     */
    private function chargeCustomer($token, $shoppingCart, StripeClient $stripeClient, SubscriptionHelper $subscriptionHelper)
    {
        if (!$token && $shoppingCart->getTotalWithDiscount() > 0) {
            throw new \Exception('Somehow the order is non-free, but we have no token!?');
        }

        //$stripeClient = $this->stripeClient;
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getStripeCustomerId()) {
            $stripeCustomer = $stripeClient->createCustomer($user, $token);
        } else {
            // don't need to update it if the order is fre
            if ($token) {
                $stripeCustomer = $stripeClient->updateCustomerCard($user, $token);
            } else {
                $stripeCustomer = $stripeClient->findCustomer($user);
            }
        }

        // save card details
        //$subscriptionHelper->updateCardDetails($user, $stripeCustomer);

        $cart = $shoppingCart;

        if ($cart->getCouponCodeValue()) {
            $stripeCustomer->coupon = $cart->getCouponCode();
            $stripeCustomer->save();
        }

        foreach ($cart->getProducts() as $product) {
            $stripeClient->createInvoiceItem(
                $product->getPrice() * 100,
                $user,
                $product->getName()
            );
        }

        if ($cart->getSubscriptionPlan()) {
            // a subscription creates an invoice
            $stripeSubscription = $stripeClient->createSubscription(
                $user,
                $cart->getSubscriptionPlan()
            );

            $subscriptionHelper->addSubscriptionToUser(
                $stripeSubscription,
                $user
            );
        } else {
            // charge the invoice!
            $stripeClient->createInvoice($user, true);
        }
    }
}
