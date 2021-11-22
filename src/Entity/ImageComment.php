<?php

namespace App\Entity;

use App\Repository\ImageCommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageCommentRepository::class)
 */
class ImageComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Images::class, inversedBy="imageComments")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="imageComments")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=ImageComment::class, inversedBy="imageComments", fetch="EXTRA_LAZY")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=ImageComment::class, mappedBy="parent", orphanRemoval=true)
     */
    private $imageComments;

    public function __construct()
    {
        $this->imageComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?Images
    {
        return $this->image;
    }

    public function setImage(?Images $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getImageComments(): Collection
    {
        return $this->imageComments;
    }

    public function addImageComment(self $imageComment): self
    {
        if (!$this->imageComments->contains($imageComment)) {
            $this->imageComments[] = $imageComment;
            $imageComment->setParent($this);
        }

        return $this;
    }

    public function removeImageComment(self $imageComment): self
    {
        if ($this->imageComments->removeElement($imageComment)) {
            // set the owning side to null (unless already changed)
            if ($imageComment->getParent() === $this) {
                $imageComment->setParent(null);
            }
        }

        return $this;
    }
}
