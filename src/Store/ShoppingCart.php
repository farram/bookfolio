<?php

namespace App\Store;

use App\Entity\Product;
use App\Subscription\SubscriptionHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ShoppingCart
{
    public const CART_PRODUCTS_KEY = '_shopping_cart.products';
    public const CART_PLAN_KEY = '_shopping_cart.subscription_plan';
    public const CART_COUPON_CODE_KEY = '_shopping_cart.coupon_code';
    public const CART_COUPON_VALUE_KEY = '_shopping_cart.coupon_value';

    private $session;
    private $em;
    private $subscriptionHelper;

    private $products;

    public function __construct(EntityManagerInterface $em, SubscriptionHelper $subscriptionHelper)
    {
        $session = new Session();
        $this->session = $session;
        $this->em = $em;
        $this->subscriptionHelper = $subscriptionHelper;
    }

    public function addProduct(Product $product)
    {
        $products = $this->getProducts();

        if (!in_array($product, $products)) {
            $products[] = $product;
        }

        $this->updateProducts($products);
    }

    public function addSubscription($planId)
    {
        $this->session->set(
            self::CART_PLAN_KEY,
            $planId
        );
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        if (null === $this->products) {
            $productRepo = $this->em->getRepository('App:Product');
            $ids = $this->session->get(self::CART_PRODUCTS_KEY, []);
            $products = [];
            foreach ($ids as $id) {
                $product = $productRepo->find($id);

                // in case a product becomes deleted
                if ($product) {
                    $products[] = $product;
                }
            }

            $this->products = $products;
        }

        return $this->products;
    }

    /**
     * @return \App\Subscription\SubscriptionPlan|null
     */
    public function getSubscriptionPlan()
    {
        $planId = $this->session->get(self::CART_PLAN_KEY);

        return $this->subscriptionHelper
            ->findPlan($planId);
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getProducts() as $product) {
            $total += $product->getPrice();
        }

        if ($this->getSubscriptionPlan()) {
            $price = $this->getSubscriptionPlan()
                ->getPrice();

            $total += $price;
        }

        return $total;
    }

    public function getTotalWithDiscount()
    {
        return max($this->getTotal() - $this->getCouponCodeValue(), 0);
    }

    public function setCouponCode($code, $value)
    {
        $this->session->set(
            self::CART_COUPON_CODE_KEY,
            $code
        );

        $this->session->set(
            self::CART_COUPON_VALUE_KEY,
            $value
        );
    }

    public function getCouponCode()
    {
        return $this->session->get(self::CART_COUPON_CODE_KEY);
    }

    public function getCouponCodeValue()
    {
        return $this->session->get(self::CART_COUPON_VALUE_KEY);
    }

    public function emptyCart()
    {
        $this->updateProducts([]);
        $this->updatePlanId(null);
        $this->setCouponCode(null, null);
    }

    /**
     * @param Product[] $products
     */
    private function updateProducts(array $products)
    {
        $this->products = $products;

        $ids = array_map(function (Product $item) {
            return $item->getId();
        }, $products);

        $this->session->set(self::CART_PRODUCTS_KEY, $ids);
    }

    private function updatePlanId($planId)
    {
        $this->session->set(self::CART_PLAN_KEY, $planId);
    }
}
