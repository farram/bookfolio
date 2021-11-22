<?php

namespace App;

use App\Entity\User;
use App\Subscription\SubscriptionPlan;
use Doctrine\ORM\EntityManagerInterface;

class StripeClient
{
    private $em;
    protected $secretKey;
    protected $publicKey;

    public function __construct(string $secretKey, string $publicKey, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;

        //\Stripe\Stripe::setApiKey($secretKey);
        \Stripe\Stripe::setApiKey($this->secretKey);
    }

    public function createCustomer(User $user, $paymentToken)
    {
        $data = [
            'email' => $user->getEmail(),
        ];

        if ($paymentToken) {
            $data['source'] = $paymentToken;
        }

        $customer = \Stripe\Customer::create($data);
        $user->setStripeCustomerId($customer->id);

        $this->em->persist($user);
        $this->em->flush($user);

        return $customer;
    }

    public function updateCustomerCard(User $user, $paymentToken)
    {
        $customer = \Stripe\Customer::retrieve($user->getStripeCustomerId());

        $customer->source = $paymentToken;
        $customer->save();

        return $customer;
    }

    public function findCustomer(User $user)
    {
        return \Stripe\Customer::retrieve($user->getStripeCustomerId());
    }

    public function createInvoiceItem($amount, User $user, $description)
    {
        return \Stripe\InvoiceItem::create([
            'amount' => $amount,
            'currency' => 'usd',
            'customer' => $user->getStripeCustomerId(),
            'description' => $description,
        ]);
    }

    public function createInvoice(User $user, $payImmediately = true)
    {
        $invoice = \Stripe\Invoice::create([
            'customer' => $user->getStripeCustomerId(),
        ]);

        if ($payImmediately) {
            // guarantee it charges *right* now
            try {
                $invoice->pay();
            } catch (\Stripe\Error\Card $e) {
                // paying failed, close this invoice so we don't
                // keep trying to pay it
                $invoice->closed = true;
                $invoice->save();

                throw $e;
            }
        }

        return $invoice;
    }

    public function createSubscription(User $user, SubscriptionPlan $plan)
    {
        $subscription = \Stripe\Subscription::create([
            'customer' => $user->getStripeCustomerId(),
            'plan' => $plan->getPlanId(),
        ]);

        return $subscription;
    }

    public function cancelSubscription(User $user)
    {
        // dd($user->getStripeCustomerId());
        // dd($user->getSubscription()->getStripeSubscriptionId());
        $sub = \Stripe\Subscription::retrieve(
            //$user->getStripeCustomerId(),
            $user->getSubscription()->getStripeSubscriptionId()
        );

        $currentPeriodEnd = new \DateTime('@'.$sub->current_period_end);
        $cancelAtPeriodEnd = true;

        if ('past_due' == $sub->status) {
            // past due? Cancel immediately, don't try charging again
            $cancelAtPeriodEnd = false;
        } elseif ($currentPeriodEnd < new \DateTime('+1 hour')) {
            // within 1 hour of the end? Cancel so the invoice isn't charged
            $cancelAtPeriodEnd = false;
        }

        $sub->update($user->getSubscription()->getStripeSubscriptionId(), [
            'cancel_at_period_end' => $cancelAtPeriodEnd,
        ]);

        return $sub;
    }

    public function reactivateSubscription(User $user)
    {
        if (!$user->hasActiveSubscription()) {
            throw new \LogicException('Subscriptions can only be reactivated if the subscription has not actually ended yet');
        }

        $subscription = \Stripe\Subscription::retrieve(
            $user->getSubscription()->getStripeSubscriptionId()
        );
        // this triggers the refresh of the subscription!
        $subscription->plan = $user->getSubscription()->getStripePlanId();
        $subscription->save();

        return $subscription;
    }

    /**
     * @param $eventId
     *
     * @return \Stripe\Event
     */
    public function findEvent($eventId)
    {
        return \Stripe\Event::retrieve($eventId);
    }

    /**
     * @param $stripeSubscriptionId
     *
     * @return \Stripe\Subscription
     */
    public function findSubscription($stripeSubscriptionId)
    {
        return \Stripe\Subscription::retrieve($stripeSubscriptionId);
    }

    public function getUpcomingInvoiceForChangedSubscription(User $user, SubscriptionPlan $newPlan)
    {
        return \Stripe\Invoice::upcoming([
            'customer' => $user->getStripeCustomerId(),
            'subscription' => $user->getSubscription()->getStripeSubscriptionId(),
            'subscription_plan' => $newPlan->getPlanId(),
        ]);
    }

    public function changePlan(User $user, SubscriptionPlan $newPlan)
    {
        $stripeSubscription = $this->findSubscription($user->getSubscription()->getStripeSubscriptionId());

        $currentPeriodStart = $stripeSubscription->current_period_start;

        $originalPlanId = $stripeSubscription->plan->id;
        $stripeSubscription->plan = $newPlan->getPlanId();
        $stripeSubscription->save();

        // if the duration did not change, Stripe will not charge them immediately
        // but we *do* want them to be charged immediately
        // if the duration changed, an invoice was already created and paid
        if ($stripeSubscription->current_period_start == $currentPeriodStart) {
            try {
                // immediately invoice them
                $this->createInvoice($user);
            } catch (\Stripe\Error\Card $e) {
                $stripeSubscription->plan = $originalPlanId;
                // prevent prorations discounts/charges from changing back
                $stripeSubscription->prorate = false;
                $stripeSubscription->save();

                throw $e;
            }
        }

        return $stripeSubscription;
    }

    /**
     * @param $code
     *
     * @return \Stripe\Coupon
     */
    public function findCoupon($code)
    {
        return \Stripe\Coupon::retrieve($code);
    }

    /**
     * @return \Stripe\Invoice[]
     */
    public function findPaidInvoices(User $user)
    {
        if ($user->getStripeCustomerId()) {
            $allInvoices = \Stripe\Invoice::all([
                'customer' => $user->getStripeCustomerId(),
            ]);

            $iterator = $allInvoices->autoPagingIterator();
            $invoices = [];
            foreach ($iterator as $invoice) {
                if ($invoice->paid) {
                    $invoices[] = $invoice;
                }
            }

            return $invoices;
        }
    }

    public function findInvoice($invoiceId)
    {
        return \Stripe\Invoice::retrieve($invoiceId);
    }
}
