<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventsRepository::class)
 */
class Events
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getIcon()
    {
        switch ($this->getType()) {
            case 'follow':
                return 'mdi mdi-account-plus';
                break;
            case 'comment':
                return 'mdi mdi-comment-account-outline';
                break;
            case 'like':
                return 'mdi mdi-heart';
                break;

            default:
                break;
        }
    }

    public function getColor()
    {
        switch ($this->getType()) {
            case 'follow':
                return 'bg-blue';
                break;
            case 'comment':
                return 'bg-blue';
                break;
            case 'like':
                return 'bg-pink';
                break;

            default:
                break;
        }
    }
}
