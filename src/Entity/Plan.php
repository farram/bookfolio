<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanRepository::class)
 */
class Plan
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
    private $planName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $planPrice;

    /**
     * @ORM\OneToMany(targetEntity=PlanDetails::class, mappedBy="plan")
     */
    private $planDetails;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHighlight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $publication;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idPriceApi;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity=Design::class, mappedBy="plan")
     */
    private $designs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mostPopular;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $planPriceAnnual;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $totalAnnual;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idPriceApiAnnual;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function __toString()
    {
        return $this->planName;
    }

    public function __construct()
    {
        $this->planDetails = new ArrayCollection();
        $this->designs = new ArrayCollection();
        $this->design = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanName(): ?string
    {
        return $this->planName;
    }

    public function setPlanName(?string $planName): self
    {
        $this->planName = $planName;

        return $this;
    }

    public function getPlanPrice(): ?string
    {
        return $this->planPrice;
    }

    public function setPlanPrice(?string $planPrice): string
    {
        $this->planPrice = $planPrice;

        return $this;
    }

    /**
     * @return Collection|PlanDetails[]
     */
    public function getPlanDetails(): Collection
    {
        return $this->planDetails;
    }

    public function addPlanDetail(PlanDetails $planDetail): self
    {
        if (!$this->planDetails->contains($planDetail)) {
            $this->planDetails[] = $planDetail;
            $planDetail->setPlan($this);
        }

        return $this;
    }

    public function removePlanDetail(PlanDetails $planDetail): self
    {
        if ($this->planDetails->removeElement($planDetail)) {
            // set the owning side to null (unless already changed)
            if ($planDetail->getPlan() === $this) {
                $planDetail->setPlan(null);
            }
        }

        return $this;
    }

    public function getIsHighlight(): ?bool
    {
        return $this->isHighlight;
    }

    public function setIsHighlight(?bool $isHighlight): self
    {
        $this->isHighlight = $isHighlight;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getPublication(): ?int
    {
        return $this->publication;
    }

    public function setPublication(int $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getBadgeColor(): ?string
    {
        if ('starter' == $this->getSlug()) {
            return 'badge-light-dark';
        } elseif ('awesome' == $this->getSlug()) {
            return 'badge-light-success';
        } elseif ('pro' == $this->getSlug()) {
            return 'badge-light-info';
        }
    }

    public function getIconColor(): ?string
    {
        if ('starter' == $this->getSlug()) {
            return 'secondary';
        } elseif ('awesome' == $this->getSlug()) {
            return 'success';
        } elseif ('pro' == $this->getSlug()) {
            return 'blue';
        }
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getIdPriceApi(): ?string
    {
        return $this->idPriceApi;
    }

    public function setIdPriceApi(?string $idPriceApi): self
    {
        $this->idPriceApi = $idPriceApi;

        return $this;
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

    /**
     * @return Collection|Design[]
     */
    public function getDesigns(): Collection
    {
        return $this->designs;
    }

    public function addDesign(Design $design): self
    {
        if (!$this->designs->contains($design)) {
            $this->designs[] = $design;
            $design->addPlan($this);
        }

        return $this;
    }

    public function removeDesign(Design $design): self
    {
        if ($this->designs->removeElement($design)) {
            $design->removePlan($this);
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getMostPopular(): ?int
    {
        return $this->mostPopular;
    }

    public function setMostPopular(?int $mostPopular): self
    {
        $this->mostPopular = $mostPopular;

        return $this;
    }

    public function getPlanPriceAnnual(): ?string
    {
        return $this->planPriceAnnual;
    }

    public function setPlanPriceAnnual(string $planPriceAnnual): self
    {
        $this->planPriceAnnual = $planPriceAnnual;

        return $this;
    }

    public function getTotalAnnual(): ?string
    {
        return $this->totalAnnual;
    }

    public function setTotalAnnual(?string $totalAnnual): self
    {
        $this->totalAnnual = $totalAnnual;

        return $this;
    }

    public function getIdPriceApiAnnual(): ?string
    {
        return $this->idPriceApiAnnual;
    }

    public function setIdPriceApiAnnual(?string $idPriceApiAnnual): self
    {
        $this->idPriceApiAnnual = $idPriceApiAnnual;

        return $this;
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
}
