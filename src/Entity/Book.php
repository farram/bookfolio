<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Vous arrivez trop tard...Ce nom de book est déjà pris !"
 * )
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="book", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=false,unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowSeo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codeAnalytics;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showContact;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $allowComments;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showVisitorCounter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $stylePhotos;

    /**
     * @ORM\ManyToOne(targetEntity=Design::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $design;

    public function __toString()
    {
        return $this->name;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getAllowSeo(): ?bool
    {
        return $this->allowSeo;
    }

    public function setAllowSeo(?bool $allowSeo): self
    {
        $this->allowSeo = $allowSeo;

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

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getCodeAnalytics(): ?string
    {
        return $this->codeAnalytics;
    }

    public function setCodeAnalytics(?string $codeAnalytics): self
    {
        $this->codeAnalytics = $codeAnalytics;

        return $this;
    }

    public function getShowContact(): ?bool
    {
        return $this->showContact;
    }

    public function setShowContact(?bool $showContact): self
    {
        $this->showContact = $showContact;

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

    public function getShowVisitorCounter(): ?bool
    {
        return $this->showVisitorCounter;
    }

    public function setShowVisitorCounter(?bool $showVisitorCounter): self
    {
        $this->showVisitorCounter = $showVisitorCounter;

        return $this;
    }

    public function getListStylePhotos(): ?string
    {
        return $this->stylePhotos;
    }

    public function getStylePhotos(): ?array
    {
        $content = filter_var($this->stylePhotos, FILTER_SANITIZE_STRING);
        $content = explode(',', $content);

        return $content;
    }

    public function getStylePhotosUser(): ?string
    {
        return '';
    }

    public function setStylePhotos(?array $stylePhotos): self
    {
        $list = [];
        foreach ($stylePhotos as $style) {
            $list[] = $style->getId();
        }
        $this->stylePhotos = (!empty($stylePhotos) ? implode(',', $list) : '');

        return $this;
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->stylePhotos) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getDesign(): ?Design
    {
        return $this->design;
    }

    public function setDesign(?Design $design): self
    {
        $this->design = $design;

        return $this;
    }
}
