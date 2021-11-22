<?php

namespace App\Subscription;

use App\Entity\User;
use App\Entity\Subscription;
use App\Repository\PlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlanDetailsRepository;

class SubscriptionHelper
{
    /** @var SubscriptionPlan[] */
    private $plans = [];
    private $em;

    private $planRepository;
    private $planDetailsRepository;

    public const FREE_COUNT_MAX_IMAGES = '10';
    public const FREE_COUNT_MAX_GALLERY = '5';
    public const FREE_COUNT_MAX_VIDEOS = '5';
    public const FREE_COUNT_MAX_ANNONCES = '2';
    public const FREE_CAN_VIEW_STATS = false;
    public const FREE_CAN_ACCESS_TEMPLATE_PREMIEUM = false;
    public const FREE_CAN_ACCESS_CUSTOM_DOMAIN = false;
    public const FREE_CAN_GALLERY_PRIVATE = false;
    public const FREE_CAN_ACCESS_INBOX = false;

    public function __construct(EntityManagerInterface $em, PlanRepository $planRepository, PlanDetailsRepository $planDetailsRepository)
    {
        $this->em = $em;
        $this->planRepository = $planRepository;
        $this->planDetailsRepository = $planDetailsRepository;

        $this->plans[] = new SubscriptionPlan(
            'price_1JxCtOFekYzBq4jwQV7aeMoE',
            'Awesome',
            199
        );

        $this->plans[] = new SubscriptionPlan(
            'price_1JxCu3FekYzBq4jwQ5dLzbGK',
            'Pro',
            599
        );

        $this->plans[] = new SubscriptionPlan(
            'price_1JxrSOFekYzBq4jwfckoT08q',
            'Awesome',
            2199,
            SubscriptionPlan::DURATION_YEARLY
        );

        $this->plans[] = new SubscriptionPlan(
            'price_1JxraCFekYzBq4jw4eltkQ9X',
            'Pro',
            6499,
            SubscriptionPlan::DURATION_YEARLY
        );
    }

    /**
     * @param $planId
     *
     * @return SubscriptionPlan|null
     */
    public function findPlan($planId)
    {
        foreach ($this->plans as $plan) {
            if ($plan->getPlanId() == $planId) {
                return $plan;
            }
        }
    }

    /**
     * @param $currentPlanId
     *
     * @return SubscriptionPlan
     */
    public function findPlanToChangeTo($currentPlanId)
    {
        if (false !== strpos($currentPlanId, 'price_1INSeaFekYzBq4jwckgIVjXJ')) {
            $newPlanId = str_replace('price_1INSeaFekYzBq4jwckgIVjXJ', 'price_1IhxtUFekYzBq4jwRD57c3u6', $currentPlanId);
        } else {
            $newPlanId = str_replace('price_1IhxtUFekYzBq4jwRD57c3u6', 'price_1INSeaFekYzBq4jwckgIVjXJ', $currentPlanId);
        }

        return $this->findPlan($newPlanId);
    }

    public function findPlanForOtherDuration($currentPlanId)
    {
        if (false !== strpos($currentPlanId, 'monthly')) {
            $newPlanId = str_replace('monthly', 'yearly', $currentPlanId);
        } else {
            $newPlanId = str_replace('yearly', 'monthly', $currentPlanId);
        }

        return $this->findPlan($newPlanId);
    }

    public function addSubscriptionToUser(\Stripe\Subscription $stripeSubscription, User $user)
    {
        $subscription = $user->getSubscription();
        if (!$subscription) {
            $subscription = new Subscription();
            $subscription->setUser($user);
        }

        $periodEnd = \DateTime::createFromFormat('U', $stripeSubscription->current_period_end);
        $subscription->activateSubscription(
            $stripeSubscription->plan->id,
            $stripeSubscription->id,
            $periodEnd
        );

        $this->em->persist($subscription);
        $this->em->flush($subscription);
    }

    public function fullyCancelSubscription(Subscription $subscription)
    {
        $subscription->cancel();
        $this->em->persist($subscription);
        $this->em->flush($subscription);
    }

    public function handleSubscriptionPaid(Subscription $subscription, \Stripe\Subscription $stripeSubscription)
    {
        $newPeriodEnd = \DateTime::createFromFormat('U', $stripeSubscription->current_period_end);

        // you can use this to send emails to new or renewal customers
        $isRenewal = $newPeriodEnd > $subscription->getBillingPeriodEndsAt();

        $subscription->setBillingPeriodEndsAt($newPeriodEnd);
        $this->em->persist($subscription);
        $this->em->flush($subscription);
    }

    public function getAvalableFree()
    {
        $plan = $this->planRepository->findOneBy(['slug' => 'starter']);
        $planDetails = $this->planDetailsRepository->findOneBy(['plan' => $plan->getId(), 'type' => 'images']);
        return $planDetails->getValue();
    }

    public function getAvalableFreeGallery()
    {
        $plan = $this->planRepository->findOneBy(['slug' => 'starter']);
        $planDetails = $this->planDetailsRepository->findOneBy(['plan' => $plan->getId(), 'type' => 'gallery']);
        return $planDetails->getValue();
    }

    public function getAvalableFreeVideo()
    {
        $plan = $this->planRepository->findOneBy(['slug' => 'starter']);
        $planDetails = $this->planDetailsRepository->findOneBy(['plan' => $plan->getId(), 'type' => 'videos']);
        return $planDetails->getValue();
    }

    public function getAvalableFreeAnnonces()
    {
        $plan = $this->planRepository->findOneBy(['slug' => 'starter']);
        $planDetails = $this->planDetailsRepository->findOneBy(['plan' => $plan->getId(), 'type' => 'annonces']);
        return $planDetails->getValue();
    }
}
