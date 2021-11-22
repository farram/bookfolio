<?php

namespace App\Subscription;

class SubscriptionPlan
{
    public const DURATION_MONTHLY = 'monthly';
    public const DURATION_YEARLY = 'yearly';

    private $planId;

    private $name;

    private $price;

    private $duration;

    public function __construct($planId, $name, $price, $duration = self::DURATION_MONTHLY)
    {
        $this->planId = $planId;
        $this->name = $name;
        $this->price = $price;
        $this->duration = $duration;
    }

    public function getPlanId()
    {
        return $this->planId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getColor()
    {
        if ('price_1INSeaFekYzBq4jwckgIVjXJ' == $this->getPlanId()) {
            return 'warning';
        } elseif ('price_1IhxtUFekYzBq4jwRD57c3u6' == $this->getPlanId()) {
            return 'primary';
        }
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getDuration()
    {
        return $this->duration;
    }
}
