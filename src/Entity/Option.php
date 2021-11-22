<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OptionRepository::class)
 */
class Option
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="option", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $contactPublishMedias;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $contactShareMessage;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $contactSendPrivateMessage;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $commentImage;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $follow;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $darkMode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContactPublishMedias(): ?bool
    {
        return $this->contactPublishMedias;
    }

    public function setContactPublishMedias(?bool $contactPublishMedias): self
    {
        $this->contactPublishMedias = $contactPublishMedias;

        return $this;
    }

    public function getContactShareMessage(): ?bool
    {
        return $this->contactShareMessage;
    }

    public function setContactShareMessage(?bool $contactShareMessage): self
    {
        $this->contactShareMessage = $contactShareMessage;

        return $this;
    }

    public function getContactSendPrivateMessage(): ?bool
    {
        return $this->contactSendPrivateMessage;
    }

    public function setContactSendPrivateMessage(?bool $contactSendPrivateMessage): self
    {
        $this->contactSendPrivateMessage = $contactSendPrivateMessage;

        return $this;
    }

    public function getCommentImage(): ?bool
    {
        return $this->commentImage;
    }

    public function setCommentImage(?bool $commentImage): self
    {
        $this->commentImage = $commentImage;

        return $this;
    }

    public function getFollow(): ?bool
    {
        return $this->follow;
    }

    public function setFollow(?bool $follow): self
    {
        $this->follow = $follow;

        return $this;
    }

    public function getDarkMode(): ?int
    {
        return $this->darkMode;
    }

    public function setDarkMode(?int $darkMode): self
    {
        $this->darkMode = $darkMode;

        return $this;
    }
}
