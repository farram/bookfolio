<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use App\Service\FileUploader;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="images")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isVisible;

    /**
     * @ORM\ManyToOne(targetEntity=Gallery::class, inversedBy="images", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $gallery;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=ImageView::class, mappedBy="image", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $imageViews;

    /**
     * @ORM\OneToMany(targetEntity=ImageComment::class, mappedBy="image", orphanRemoval=true, cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $imageComments;

    /**
     * @ORM\OneToMany(targetEntity=ImageLike::class, mappedBy="image", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $imageLikes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $copyright;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isNSFW;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHome;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isGallery;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowFavorites;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowLikes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowComments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="integer")
     */
    private $countView;

    public function __construct()
    {
        $this->imageViews = new ArrayCollection();
        $this->imageComments = new ArrayCollection();
        $this->imageLikes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->imageName;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getImagePath()
    {
        return UploaderHelper::MEDIAS_USER.'/'.$this->user->getId().'/'.$this->gallery->getId().'/'.$this->getImageName();
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(?bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    public function setGallery(?Gallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getThumbXs()
    {
        return $this->user->getId().'/'.$this->gallery->getId().'/thumbs/small_xs/'.$this->imageName;
    }

    public function getThumbMd()
    {
        return $this->user->getId().'/'.$this->gallery->getId().'/thumbs/small_md/'.$this->imageName;
    }

    public function getThumb()
    {
        return $this->user->getId().'/'.$this->gallery->getId().'/'.$this->imageName;
    }

    public function getCredentials()
    {
        $configAws = $this->imageHelper->getAws();

        return new FileUploader::$S3('files', $configAws);
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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

    /**
     * @return Collection|ImageView[]
     */
    public function getImageViews(): Collection
    {
        return $this->imageViews;
    }

    public function addImageView(ImageView $imageView): self
    {
        if (!$this->imageViews->contains($imageView)) {
            $this->imageViews[] = $imageView;
            $imageView->setImage($this);
        }

        return $this;
    }

    public function removeImageView(ImageView $imageView): self
    {
        if ($this->imageViews->removeElement($imageView)) {
            // set the owning side to null (unless already changed)
            if ($imageView->getImage() === $this) {
                $imageView->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ImageComment[]
     */
    public function getImageComments(): Collection
    {
        return $this->imageComments;
    }

    public function addImageComment(ImageComment $imageComment): self
    {
        if (!$this->imageComments->contains($imageComment)) {
            $this->imageComments[] = $imageComment;
            $imageComment->setImage($this);
        }

        return $this;
    }

    public function removeImageComment(ImageComment $imageComment): self
    {
        if ($this->imageComments->removeElement($imageComment)) {
            // set the owning side to null (unless already changed)
            if ($imageComment->getImage() === $this) {
                $imageComment->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ImageLike[]
     */
    public function getImageLikes(): Collection
    {
        return $this->imageLikes;
    }

    public function addImageLike(ImageLike $imageLike): self
    {
        if (!$this->imageLikes->contains($imageLike)) {
            $this->imageLikes[] = $imageLike;
            $imageLike->setImage($this);
        }

        return $this;
    }

    public function removeImageLike(ImageLike $imageLike): self
    {
        if ($this->imageLikes->removeElement($imageLike)) {
            // set the owning side to null (unless already changed)
            if ($imageLike->getImage() === $this) {
                $imageLike->setImage(null);
            }
        }

        return $this;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): self
    {
        $this->copyright = $copyright;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getIsNSFW(): ?bool
    {
        return $this->isNSFW;
    }

    public function setIsNSFW(bool $isNSFW): self
    {
        $this->isNSFW = $isNSFW;

        return $this;
    }

    public function getIsHome(): ?bool
    {
        return $this->isHome;
    }

    public function setIsHome(bool $isHome): self
    {
        $this->isHome = $isHome;

        return $this;
    }

    public function getIsGallery(): ?bool
    {
        return $this->isGallery;
    }

    public function setIsGallery(bool $isGallery): self
    {
        $this->isGallery = $isGallery;

        return $this;
    }

    public function getAllowFavorites(): ?bool
    {
        return $this->allowFavorites;
    }

    public function setAllowFavorites(bool $allowFavorites): self
    {
        $this->allowFavorites = $allowFavorites;

        return $this;
    }

    public function getAllowLikes(): ?bool
    {
        return $this->allowLikes;
    }

    public function setAllowLikes(bool $allowLikes): self
    {
        $this->allowLikes = $allowLikes;

        return $this;
    }

    public function getAllowComments(): ?bool
    {
        return $this->allowComments;
    }

    public function setAllowComments(?bool $allowComments): self
    {
        $this->allowComments = $allowComments;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function findImagesUploadThisMonth($month)
    {
        return 100;
    }

    public function getCountView(): ?int
    {
        return $this->countView;
    }

    public function setCountView(int $countView): self
    {
        $this->countView = $countView;

        return $this;
    }
}
