<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=GalleryRepository::class)
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="galleries")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isProtect;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordHash;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAtPassword;

    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\InversedRelativeSlugHandler", options={
     *      @Gedmo\SlugHandlerOption(name="relationClass", value="App\Entity\User"),
     *      @Gedmo\SlugHandlerOption(name="mappedBy", value="id"),
     *      @Gedmo\SlugHandlerOption(name="inverseSlugField", value="slug")
     *      })
     * }, separator="-", updatable=true, fields={"name"})
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="gallery")
     */
    private $images;

    /**
     * @ORM\OneToOne(targetEntity=Images::class, cascade={"persist", "remove"})
     */
    private $coverImage;

    /**
     * @ORM\OneToMany(targetEntity=GalleryView::class, mappedBy="gallery")
     */
    private $galleryViews;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->setUpdatedAt(new \DateTime());
        $this->galleryViews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function setCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getIsProtect(): ?bool
    {
        return $this->isProtect;
    }

    public function setIsProtect(?bool $isProtect): self
    {
        $this->isProtect = $isProtect;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(?string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    public function getCreatedAtPassword(): ?\DateTimeInterface
    {
        return $this->createdAtPassword;
    }

    public function setCreatedAtPassword(?\DateTimeInterface $createdAtPassword): self
    {
        $this->createdAtPassword = $createdAtPassword;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setGallery($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getGallery() === $this) {
                $image->setGallery(null);
            }
        }

        return $this;
    }

    public function getCoverImage(): ?Images
    {
        return $this->coverImage;

        /*if($this->coverImage !== null){
            return $this->coverImage;
        }else{
            return $this->getLastPublishedImage();
        }*/
    }

    public function setCoverImage(?Images $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * @return Collection|GalleryView[]
     */
    public function getGalleryViews(): Collection
    {
        return $this->galleryViews;
    }

    public function addGalleryView(GalleryView $galleryView): self
    {
        if (!$this->galleryViews->contains($galleryView)) {
            $this->galleryViews[] = $galleryView;
            $galleryView->setGallery($this);
        }

        return $this;
    }

    public function removeGalleryView(GalleryView $galleryView): self
    {
        if ($this->galleryViews->removeElement($galleryView)) {
            // set the owning side to null (unless already changed)
            if ($galleryView->getGallery() === $this) {
                $galleryView->setGallery(null);
            }
        }

        return $this;
    }

    public function getLastImage()
    {
        // Si l'utilisateur a posté au moins une photo
        if (count($this->getImages()) > 0) {
            // findLastFromGallery
            return true;
        } else {
            return false;
        }
    }

    public function getNoThumb()
    {
        return UploaderHelper::NOTHUMB;
    }

    public function getLocked()
    {
        return UploaderHelper::LOCKED;
    }
}
