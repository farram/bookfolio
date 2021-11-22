<?php

namespace App\Entity;

use App\Repository\PlanFeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanFeatureRepository::class)
 */
class PlanFeature
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=PlanDetails::class, mappedBy="planFeatureId")
     */
    private $planDetails;

    /**
     * @ORM\Column(type="integer")
     */
    private $showDetails;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $details;

    public function __construct()
    {
        $this->planDetails = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
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
            $planDetail->setPlanFeatureId($this);
        }

        return $this;
    }

    public function removePlanDetail(PlanDetails $planDetail): self
    {
        if ($this->planDetails->removeElement($planDetail)) {
            // set the owning side to null (unless already changed)
            if ($planDetail->getPlanFeatureId() === $this) {
                $planDetail->setPlanFeatureId(null);
            }
        }

        return $this;
    }

    public function getShowDetails(): ?int
    {
        return $this->showDetails;
    }

    public function setShowDetails(int $showDetails): self
    {
        $this->showDetails = $showDetails;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }
}
