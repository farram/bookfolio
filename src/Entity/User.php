<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable()
 * @UniqueEntity(
 * fields= {"email"},
 * message = "Cette adresse email est déjà utilisé !"
 * )
 */
class User implements UserInterface
{
    public const GENDER = [
        'Aucune importance' => 0,
        'Homme' => 1,
        'Femme' => 2,
        'Autre' => 3,
    ];

    public function __construct()
    {
        $this->address = new Address();
        $this->roles = ['ROLE_USER'];
        $this->updatedAt = new \DateTime();
        $this->annonces = new ArrayCollection();
        $this->annoncesComments = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->imageComments = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->statistics = new ArrayCollection();
        $this->guestbooks = new ArrayCollection();
        $this->follows = new ArrayCollection();
        $this->unsuggestBooks = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->inboxes = new ArrayCollection();
        $this->notSuggesteds = new ArrayCollection();
        $this->releaseNotes = new ArrayCollection();
        $this->testimonials = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $stripeCustomerId;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups("main")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\ManyToOne(targetEntity=Profession::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $profession;

    /**
     * @ORM\ManyToOne(targetEntity=Experience::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $experience;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="guid",unique=false)
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=500, nullable=false)
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, mappedBy="user",cascade={"persist"}))
     * @Embedded(class = "Address")
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity=Physical::class, mappedBy="user", cascade={"persist", "remove"})
     * @Embedded(class = "Physical")
     */
    private $physical;

    /**
     * @ORM\OneToOne(targetEntity=Option::class, mappedBy="user")
     * @Embedded(class = "Option")
     */
    private $option;

    /**
     * @ORM\OneToOne(targetEntity=Social::class, mappedBy="user", cascade={"persist", "remove"})
     * @Embedded(class = "Social")
     */
    private $social;

    /**
     * @ORM\OneToOne(targetEntity=Subscription::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $subscription;

    /**
     * @ORM\OneToOne(targetEntity=Book::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $book;

    /**
     * @ORM\OneToMany(targetEntity=Annonces::class, mappedBy="user")
     */
    private $annonces;

    /**
     * @ORM\OneToMany(targetEntity=AnnoncesComment::class, mappedBy="user")
     */
    private $annoncesComments;

    /**
     * @ORM\OneToMany(targetEntity=Gallery::class, mappedBy="user")
     */
    private $galleries;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="user")
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=ImageComment::class, mappedBy="user")
     */
    private $imageComments;

    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="user")
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity=Page::class, mappedBy="user")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity=Statistic::class, mappedBy="user")
     */
    private $statistics;

    /**
     * @ORM\OneToMany(targetEntity=Guestbook::class, mappedBy="user")
     */
    private $guestbooks;

    /**
     * @ORM\OneToMany(targetEntity=Follow::class, mappedBy="user")
     */
    private $follows;

    /**
     * @ORM\OneToMany(targetEntity=UnsuggestBook::class, mappedBy="user")
     */
    private $unsuggestBooks;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="userToNotify")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity=Inbox::class, mappedBy="book")
     */
    private $inboxes;

    /**
     * @ORM\OneToMany(targetEntity=NotSuggested::class, mappedBy="user")
     */
    private $notSuggesteds;

    /**
     * @ORM\OneToMany(targetEntity=ReleaseNotes::class, mappedBy="user")
     */
    private $releaseNotes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDemo;

    /**
     * @ORM\OneToMany(targetEntity=Testimonial::class, mappedBy="user", orphanRemoval=true)
     */
    private $testimonials;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="user", orphanRemoval=true)
     */
    private $avis;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $googleId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function setExperience(?Experience $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAvatar()
    {
        return '/' . $this->getThumbnail();
    }

    public function getFullname()
    {
        return trim(ucfirst($this->getFirstname()) . ' ' . ucfirst($this->getLastname()));
    }

    public function getFullnameDisplay()
    {
        return trim(ucfirst(strtolower($this->getFirstname())) . ' ' . $this->getUppercaseLastname());
    }

    public function getUppercaseLastname()
    {
        return strtoupper($this->getLastname());
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): self
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getThumbnailFile()
    {
        return $this->thumbnailFile;
    }

    /**
     * @param mixed $thumbnailFile
     */
    public function setThumbnailFile($thumbnailFile): void
    {
        $this->thumbnailFile = $thumbnailFile;

        if ($thumbnailFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getThumbnail(): ?string
    {
        return $this->getId() . '/' . $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getUser(): ?self
    {
        return $this;
    }

    public function getOption(): ?Option
    {
        return $this->option;
    }

    public function getPhysical(): ?Physical
    {
        return $this->physical;
    }

    public function setPhysical(?Physical $physical): self
    {
        $this->physical = $physical;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $physical ? null : $this;
        if ($physical->getUser() !== $newUser) {
            $physical->setUser($newUser);
        }

        return $this;
    }

    public function getSocial(): ?Social
    {
        return $this->social;
    }

    public function setSocial(?Social $social): self
    {
        $this->social = $social;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $social ? null : $this;
        if ($social->getUser() !== $newUser) {
            $social->setUser($newUser);
        }

        return $this;
    }

    public function getStripeCustomerId()
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId($stripeCustomerId)
    {
        $this->stripeCustomerId = $stripeCustomerId;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $subscription ? null : $this;
        if ($subscription->getUser() !== $newUser) {
            $subscription->setUser($newUser);
        }

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $book ? null : $this;
        if ($book->getUser() !== $newUser) {
            $book->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|Annonces[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonces $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonces $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
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
            $annoncesComment->setUser($this);
        }

        return $this;
    }

    public function removeAnnoncesComment(AnnoncesComment $annoncesComment): self
    {
        if ($this->annoncesComments->removeElement($annoncesComment)) {
            // set the owning side to null (unless already changed)
            if ($annoncesComment->getUser() === $this) {
                $annoncesComment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(Gallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setUser($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getUser() === $this) {
                $gallery->setUser(null);
            }
        }

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
            $image->setUser($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getUser() === $this) {
                $image->setUser(null);
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
            $imageComment->setUser($this);
        }

        return $this;
    }

    public function removeImageComment(ImageComment $imageComment): self
    {
        if ($this->imageComments->removeElement($imageComment)) {
            // set the owning side to null (unless already changed)
            if ($imageComment->getUser() === $this) {
                $imageComment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setUser($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getUser() === $this) {
                $video->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setUser($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getUser() === $this) {
                $page->setUser(null);
            }
        }

        return $this;
    }

    public function getLinkBook()
    {
        return 'test.book-folio.fr';
    }

    /**
     * @return Collection|Statistic[]
     */
    public function getStatistics(): Collection
    {
        return $this->statistics;
    }

    public function addStatistic(Statistic $statistic): self
    {
        if (!$this->statistics->contains($statistic)) {
            $this->statistics[] = $statistic;
            $statistic->setUser($this);
        }

        return $this;
    }

    public function removeStatistic(Statistic $statistic): self
    {
        if ($this->statistics->removeElement($statistic)) {
            // set the owning side to null (unless already changed)
            if ($statistic->getUser() === $this) {
                $statistic->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Guestbook[]
     */
    public function getGuestbooks(): Collection
    {
        return $this->guestbooks;
    }

    public function addGuestbook(Guestbook $guestbook): self
    {
        if (!$this->guestbooks->contains($guestbook)) {
            $this->guestbooks[] = $guestbook;
            $guestbook->setUser($this);
        }

        return $this;
    }

    public function removeGuestbook(Guestbook $guestbook): self
    {
        if ($this->guestbooks->removeElement($guestbook)) {
            // set the owning side to null (unless already changed)
            if ($guestbook->getUser() === $this) {
                $guestbook->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Follow[]
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(Follow $follow): self
    {
        if (!$this->follows->contains($follow)) {
            $this->follows[] = $follow;
            $follow->setUser($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): self
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getUser() === $this) {
                $follow->setUser(null);
            }
        }

        return $this;
    }

    public function getCertified()
    {
        if (count($this->images) >= 14) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Collection|UnsuggestBook[]
     */
    public function getUnsuggestBooks(): Collection
    {
        return $this->unsuggestBooks;
    }

    public function addUnsuggestBook(UnsuggestBook $unsuggestBook): self
    {
        if (!$this->unsuggestBooks->contains($unsuggestBook)) {
            $this->unsuggestBooks[] = $unsuggestBook;
            $unsuggestBook->setUser($this);
        }

        return $this;
    }

    public function removeUnsuggestBook(UnsuggestBook $unsuggestBook): self
    {
        if ($this->unsuggestBooks->removeElement($unsuggestBook)) {
            // set the owning side to null (unless already changed)
            if ($unsuggestBook->getUser() === $this) {
                $unsuggestBook->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('seenByUser', 'no'));

        return $this->notifications->matching($criteria);
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUserToNotify($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUserToNotify() === $this) {
                $notification->setUserToNotify(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Inbox[]
     */
    public function getUnreadinboxes(): Collection
    {
        $expression = Criteria::expr();
        $criteria = Criteria::create()->where($expression->eq('status', 'unread'));

        return $this->inboxes->matching($criteria);
    }

    /**
     * @return Collection|Inbox[]
     */
    public function getFavoritesInboxes(): Collection
    {
        $expression = Criteria::expr();
        $criteria = Criteria::create()->where($expression->eq('isFavorites', true));

        return $this->inboxes->matching($criteria);
    }

    public function getUrlBook()
    {
        return $this->book->getName();
    }

    public function hasActiveSubscription()
    {
        return $this->getSubscription() && $this->getSubscription()->isActive();
    }

    public function hasActiveNonCancelledSubscription()
    {
        return $this->hasActiveSubscription() && !$this->getSubscription()->isCancelled();
    }



    /**
     * @return Collection|NotSuggested[]
     */
    public function getNotSuggesteds(): Collection
    {
        return $this->notSuggesteds;
    }

    public function addNotSuggested(NotSuggested $notSuggested): self
    {
        if (!$this->notSuggesteds->contains($notSuggested)) {
            $this->notSuggesteds[] = $notSuggested;
            $notSuggested->setUser($this);
        }

        return $this;
    }

    public function removeNotSuggested(NotSuggested $notSuggested): self
    {
        if ($this->notSuggesteds->removeElement($notSuggested)) {
            // set the owning side to null (unless already changed)
            if ($notSuggested->getUser() === $this) {
                $notSuggested->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReleaseNotes[]
     */
    public function getReleaseNotes(): Collection
    {
        return $this->releaseNotes;
    }

    public function addReleaseNote(ReleaseNotes $releaseNote): self
    {
        if (!$this->releaseNotes->contains($releaseNote)) {
            $this->releaseNotes[] = $releaseNote;
            $releaseNote->setUser($this);
        }

        return $this;
    }

    public function removeReleaseNote(ReleaseNotes $releaseNote): self
    {
        if ($this->releaseNotes->removeElement($releaseNote)) {
            // set the owning side to null (unless already changed)
            if ($releaseNote->getUser() === $this) {
                $releaseNote->setUser(null);
            }
        }

        return $this;
    }

    public function getIsDemo(): ?bool
    {
        return $this->isDemo;
    }

    public function setIsDemo(?bool $isDemo): self
    {
        $this->isDemo = $isDemo;

        return $this;
    }

    /**
     * @return Collection|Testimonial[]
     */
    public function getTestimonials(): Collection
    {
        return $this->testimonials;
    }

    public function addTestimonial(Testimonial $testimonial): self
    {
        if (!$this->testimonials->contains($testimonial)) {
            $this->testimonials[] = $testimonial;
            $testimonial->setUser($this);
        }

        return $this;
    }

    public function removeTestimonial(Testimonial $testimonial): self
    {
        if ($this->testimonials->removeElement($testimonial)) {
            // set the owning side to null (unless already changed)
            if ($testimonial->getUser() === $this) {
                $testimonial->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setUser($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getUser() === $this) {
                $avi->setUser(null);
            }
        }

        return $this;
    }

    public function getGoogleId(): ?int
    {
        return $this->googleId;
    }

    public function setGoogleId(?int $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }
}
