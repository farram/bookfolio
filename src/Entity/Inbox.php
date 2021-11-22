<?php

namespace App\Entity;

use App\Repository\InboxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InboxRepository::class)
 */
class Inbox
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inboxes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $sender;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFavorites;

    /**
     * @ORM\OneToMany(targetEntity=InboxReply::class, mappedBy="inbox")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $inboxReplies;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_report;

    public function __construct()
    {
        $this->inboxReplies = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?User
    {
        return $this->book;
    }

    public function setBook(?User $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone($phone): self
    {
        $this->phone = $phone;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getIsFavorites(): ?bool
    {
        return $this->isFavorites;
    }

    public function setIsFavorites(bool $isFavorites): self
    {
        $this->isFavorites = $isFavorites;

        return $this;
    }

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(?int $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|InboxReply[]
     */
    public function getInboxReplies(): Collection
    {
        return $this->inboxReplies;
    }

    public function addInboxReply(InboxReply $inboxReply): self
    {
        if (!$this->inboxReplies->contains($inboxReply)) {
            $this->inboxReplies[] = $inboxReply;
            $inboxReply->setInbox($this);
        }

        return $this;
    }

    public function removeInboxReply(InboxReply $inboxReply): self
    {
        if ($this->inboxReplies->removeElement($inboxReply)) {
            // set the owning side to null (unless already changed)
            if ($inboxReply->getInbox() === $this) {
                $inboxReply->setInbox(null);
            }
        }

        return $this;
    }

    public function getIsReport(): ?bool
    {
        return $this->is_report;
    }

    public function setIsReport(bool $is_report): self
    {
        $this->is_report = $is_report;

        return $this;
    }
}
