<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="image_like",
 *    uniqueConstraints={
 *        @UniqueConstraint(name="like_unique",
 *            columns={"image_id", "user_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=ImageLikeRepository::class)
 */
class ImageLike
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Images::class, inversedBy="imageLikes")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="imageLikes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
