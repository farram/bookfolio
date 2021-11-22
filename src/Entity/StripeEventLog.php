<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="stripe_event_log")
 */
class StripeEventLog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $stripeEventId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $handledAt;

    public function __construct($stripeEventId)
    {
        $this->stripeEventId = $stripeEventId;
        $this->handledAt = new \DateTime();
    }
}
