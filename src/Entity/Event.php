<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_event')]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 160,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(
        min: 20,
        minMessage: "La description doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    #[Assert\GreaterThan(
        value: "now",
        message: "La date de début doit être dans le futur"
    )]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire")]
    #[Assert\GreaterThan(
        propertyPath: "dateStart",
        message: "La date de fin doit être après la date de début"
    )]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire")]
    #[Assert\Length(max: 160)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    #[Assert\Regex(
        pattern: "/^\d{5}$/",
        message: "Le code postal doit contenir exactement 5 chiffres"
    )]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "La ville doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $city = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le pays est obligatoire")]
    private ?string $country = null;

    #[ORM\Column(length: 160)]
    #[Assert\NotBlank(message: "Le nom de l'organisateur est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 160,
        minMessage: "Le nom de l'organisateur doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $organizer = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: "Le nombre maximum de participants doit être positif ou zéro")]
    private ?int $maxParticipants = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(name: 'id_category', referencedColumnName: 'id_category', nullable: true)]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: ToRegister::class, mappedBy: 'event', cascade: ['remove'])]
    private Collection $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Génère automatiquement le slug à partir du titre et de l'ID
     */
    public function computeSlug(SluggerInterface $slugger): static
    {
        if (!$this->slug || $this->slug === '') {
            $baseSlug = strtolower($slugger->slug($this->title)->toString());
            $this->slug = $this->id ? $baseSlug . '-' . $this->id : $baseSlug;
        }
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Retourne l'adresse complète formatée
     */
    public function getFullAddress(): string
    {
        return sprintf(
            "%s, %s %s, %s",
            $this->address,
            $this->postalCode,
            $this->city,
            $this->country
        );
    }

    public function getOrganizer(): ?string
    {
        return $this->organizer;
    }

    public function setOrganizer(string $organizer): static
    {
        $this->organizer = $organizer;
        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(?int $maxParticipants): static
    {
        $this->maxParticipants = $maxParticipants;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, ToRegister>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(ToRegister $registration): static
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations->add($registration);
            $registration->setEvent($this);
        }
        return $this;
    }

    public function removeRegistration(ToRegister $registration): static
    {
        if ($this->registrations->removeElement($registration)) {
            if ($registration->getEvent() === $this) {
                $registration->setEvent(null);
            }
        }
        return $this;
    }

    /**
     * Retourne le nombre de participants inscrits
     */
    public function getParticipantCount(): int
    {
        return $this->registrations->count();
    }

    /**
     * Vérifie si l'événement est complet
     */
    public function isFull(): bool
    {
        if ($this->maxParticipants === null) {
            return false;
        }
        return $this->getParticipantCount() >= $this->maxParticipants;
    }

    /**
     * Vérifie si l'événement est à venir
     */
    public function isUpcoming(): bool
    {
        return $this->dateStart > new \DateTime();
    }

    /**
     * Vérifie si l'événement est passé
     */
    public function isPast(): bool
    {
        return $this->dateEnd < new \DateTime();
    }

    /**
     * Vérifie si l'événement est en cours
     */
    public function isOngoing(): bool
    {
        $now = new \DateTime();
        return $this->dateStart <= $now && $this->dateEnd >= $now;
    }

    /**
     * Vérifie si un utilisateur peut s'inscrire
     */
    public function canRegister(): bool
    {
        return $this->isUpcoming() && !$this->isFull();
    }

    /**
     * Vérifie si un utilisateur est inscrit à cet événement
     */
    public function isUserRegistered(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        foreach ($this->registrations as $registration) {
            if ($registration->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne la durée de l'événement en heures
     */
    public function getDurationInHours(): float
    {
        $interval = $this->dateStart->diff($this->dateEnd);
        return ($interval->days * 24) + $interval->h + ($interval->i / 60);
    }

    /**
     * Retourne le statut de l'événement
     */
    public function getStatus(): string
    {
        if ($this->isPast()) {
            return 'past';
        }
        if ($this->isOngoing()) {
            return 'ongoing';
        }
        if ($this->isFull()) {
            return 'full';
        }
        return 'upcoming';
    }

    /**
     * Retourne le libellé du statut
     */
    public function getStatusLabel(): string
    {
        return match($this->getStatus()) {
            'past' => 'Terminé',
            'ongoing' => 'En cours',
            'full' => 'Complet',
            'upcoming' => 'À venir',
            default => 'Inconnu'
        };
    }

    /**
     * Méthode magique pour l'affichage
     */
    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
