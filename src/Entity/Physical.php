<?php

namespace App\Entity;

use App\Repository\PhysicalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhysicalRepository::class)
 */
class Physical
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="physical", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $hip;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $chest;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $confection;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $pointure;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $chestSize;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $waistSize;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $chestHeight;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $heightBust;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $backHeight;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $shoulderWidth;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $armLength;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $armsTurn;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $roundNeck;

    /**
     * @ORM\ManyToOne(targetEntity=GenderList::class, cascade={"persist", "remove"})
     */
    private $gender;

    /**
     * @ORM\ManyToOne(targetEntity=Ethnicity::class, cascade={"persist", "remove"})
     */
    private $ethnicity;

    /**
     * @ORM\ManyToOne(targetEntity=HairColor::class, cascade={"persist", "remove"})
     */
    private $hairColor;

    /**
     * @ORM\ManyToOne(targetEntity=EyesColor::class, cascade={"persist", "remove"})
     */
    private $eyesColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apnCamera;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $apnLenses;

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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSexe(): ?int
    {
        return $this->sexe;
    }

    public function setSexe(?int $sexe): self
    {
        $this->Sexe = $sexe;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHip(): ?string
    {
        return $this->hip;
    }

    public function setHip(?string $hip): self
    {
        $this->hip = $hip;

        return $this;
    }

    public function getChest(): ?string
    {
        return $this->chest;
    }

    public function setChest(?string $chest): self
    {
        $this->chest = $chest;

        return $this;
    }

    public function getConfection(): ?string
    {
        return $this->confection;
    }

    public function setConfection(?string $confection): self
    {
        $this->confection = $confection;

        return $this;
    }

    public function getPointure(): ?string
    {
        return $this->pointure;
    }

    public function setPointure(?string $pointure): self
    {
        $this->pointure = $pointure;

        return $this;
    }

    public function getChestSize(): ?string
    {
        return $this->chestSize;
    }

    public function setChestSize(?string $chestSize): self
    {
        $this->chestSize = $chestSize;

        return $this;
    }

    public function getWaistSize(): ?string
    {
        return $this->waistSize;
    }

    public function setWaistSize(?string $waistSize): self
    {
        $this->waistSize = $waistSize;

        return $this;
    }

    public function getChestHeight(): ?string
    {
        return $this->chestHeight;
    }

    public function setChestHeight(?string $chestHeight): self
    {
        $this->chestHeight = $chestHeight;

        return $this;
    }

    public function getHeightBust(): ?string
    {
        return $this->heightBust;
    }

    public function setHeightBust(?string $heightBust): self
    {
        $this->heightBust = $heightBust;

        return $this;
    }

    public function getBackHeight(): ?string
    {
        return $this->backHeight;
    }

    public function setBackHeight(?string $backHeight): self
    {
        $this->backHeight = $backHeight;

        return $this;
    }

    public function getShoulderWidth(): ?string
    {
        return $this->shoulderWidth;
    }

    public function setShoulderWidth(?string $shoulderWidth): self
    {
        $this->shoulderWidth = $shoulderWidth;

        return $this;
    }

    public function getArmLength(): ?string
    {
        return $this->armLength;
    }

    public function setArmLength(?string $armLength): self
    {
        $this->armLength = $armLength;

        return $this;
    }

    public function getArmsTurn(): ?string
    {
        return $this->armsTurn;
    }

    public function setArmsTurn(?string $armsTurn): self
    {
        $this->armsTurn = $armsTurn;

        return $this;
    }

    public function getRoundNeck(): ?string
    {
        return $this->roundNeck;
    }

    public function setRoundNeck(?string $roundNeck): self
    {
        $this->roundNeck = $roundNeck;

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

    public function getEthnicity(): ?Ethnicity
    {
        return $this->ethnicity;
    }

    public function setEthnicity(?Ethnicity $ethnicity): self
    {
        $this->ethnicity = $ethnicity;

        return $this;
    }

    public function getHairColor(): ?HairColor
    {
        return $this->hairColor;
    }

    public function setHairColor(?HairColor $hairColor): self
    {
        $this->hairColor = $hairColor;

        return $this;
    }

    public function getEyesColor(): ?EyesColor
    {
        return $this->eyesColor;
    }

    public function setEyesColor(?EyesColor $eyesColor): self
    {
        $this->eyesColor = $eyesColor;

        return $this;
    }

    public function getApnCamera(): ?string
    {
        return $this->apnCamera;
    }

    public function setApnCamera(?string $apnCamera): self
    {
        $this->apnCamera = $apnCamera;

        return $this;
    }

    public function getApnLenses(): ?string
    {
        return $this->apnLenses;
    }

    public function setApnLenses(?string $apnLenses): self
    {
        $this->apnLenses = $apnLenses;

        return $this;
    }
}
