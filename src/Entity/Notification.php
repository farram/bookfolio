<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $userToNotify;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $userWhoFiredEvent;

    /**
     * @ORM\ManyToOne(targetEntity=Events::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $seenByUser;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Images::class)
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity=ImageComment::class,cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserToNotify(): ?User
    {
        return $this->userToNotify;
    }

    public function setUserToNotify(?User $userToNotify): self
    {
        $this->userToNotify = $userToNotify;

        return $this;
    }

    public function getUserWhoFiredEvent(): ?User
    {
        return $this->userWhoFiredEvent;
    }

    public function setUserWhoFiredEvent(?User $userWhoFiredEvent): self
    {
        $this->userWhoFiredEvent = $userWhoFiredEvent;

        return $this;
    }

    public function getEvent(): ?Events
    {
        return $this->event;
    }

    public function setEvent(?Events $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getSeenByUser(): ?string
    {
        return $this->seenByUser;
    }

    public function setSeenByUser(string $seenByUser): self
    {
        $this->seenByUser = $seenByUser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMedia(): ?Images
    {
        return $this->media;
    }

    public function setMedia(?Images $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getComment(): ?ImageComment
    {
        return $this->comment;
    }

    public function setComment(?ImageComment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
