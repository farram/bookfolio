<?php

namespace App\Entity;

use App\Repository\AnnoncesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=AnnoncesRepository::class)
 */
class Annonces
{
    public const TYPE = [
        'Je recherche une collaboration' => 1,
        'Je recherche un projet rémunérer' => 2,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="annonces")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Profession::class)
     */
    private $profession;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=AnnoncesView::class, mappedBy="annonce")
     */
    private $annoncesViews;

    /**
     * @ORM\OneToMany(targetEntity=AnnoncesComment::class, mappedBy="annonce")
     */
    private $annoncesComments;

    /**
     * @ORM\ManyToOne(targetEntity=GenderList::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $gender;

    public function __construct()
    {
        $this->annoncesViews = new ArrayCollection();
        $this->annoncesComments = new ArrayCollection();
    }

    public static function getTypeSearchList()
    {
        return [
            //self::SEARCH_FREE,
            //self::SEARCH_PAYING,
        ];
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

    public function getProfession(): ?Profession
    {
        return $this->profession;
    }

    public function setProfession(?Profession $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getCreatedAt(): ?\DateTimeInterface
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|AnnoncesView[]
     */
    public function getAnnoncesViews(): Collection
    {
        return $this->annoncesViews;
    }

    public function addAnnoncesView(AnnoncesView $annoncesView): self
    {
        if (!$this->annoncesViews->contains($annoncesView)) {
            $this->annoncesViews[] = $annoncesView;
            $annoncesView->setAnnonce($this);
        }

        return $this;
    }

    public function removeAnnoncesView(AnnoncesView $annoncesView): self
    {
        if ($this->annoncesViews->removeElement($annoncesView)) {
            // set the owning side to null (unless already changed)
            if ($annoncesView->getAnnonce() === $this) {
                $annoncesView->setAnnonce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AnnoncesComment[]
     */
    public function getAnnoncesComments(): Collection
    {
        return $this->annoncesComments;
    }

    public function addAnnoncesComment(AnnoncesComment $annoncesComment): self
    {
        if (!$this->annoncesComments->contains($annoncesComment)) {
            $this->annoncesComments[] = $annoncesComment;
            $annoncesComment->setAnnonce($this);
        }

        return $this;
    }

    public function removeAnnoncesComment(AnnoncesComment $annoncesComment): self
    {
        if ($this->annoncesComments->removeElement($annoncesComment)) {
            // set the owning side to null (unless already changed)
            if ($annoncesComment->getAnnonce() === $this) {
                $annoncesComment->setAnnonce(null);
            }
        }

        return $this;
    }

    public function getGender(): ?GenderList
    {
        return $this->gender;
    }

    public function setGender(?GenderList $gender): self
    {
        $this->gender = $gender;

        return $this;
    }
}
