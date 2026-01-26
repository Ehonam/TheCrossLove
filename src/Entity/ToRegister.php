<?php

namespace App\Entity;

use App\Repository\ToRegisterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ToRegisterRepository::class)]
#[ORM\Table(name: 'to_register')]
#[UniqueEntity(
    fields: ['user', 'event'],
    message: 'Vous etes deja inscrit a cet evenement'
)]
class ToRegister
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_registration')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'id_event', referencedColumnName: 'id_event', nullable: false, onDelete: 'CASCADE')]
    private ?Event $event = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registeredAt = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $status = 'confirmed';

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: "/^\+?[0-9]{10,15}$/",
        message: "Le numero WhatsApp doit etre valide (10 a 15 chiffres)"
    )]
    private ?string $whatsappNumber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $locationUpdatedAt = null;

    public function __construct()
    {
        $this->registeredAt = new \DateTime();
        $this->status = 'confirmed';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;
        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeInterface $registeredAt): static
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getWhatsappNumber(): ?string
    {
        return $this->whatsappNumber;
    }

    public function setWhatsappNumber(?string $whatsappNumber): static
    {
        $this->whatsappNumber = $whatsappNumber;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getLocationUpdatedAt(): ?\DateTimeInterface
    {
        return $this->locationUpdatedAt;
    }

    public function setLocationUpdatedAt(?\DateTimeInterface $locationUpdatedAt): static
    {
        $this->locationUpdatedAt = $locationUpdatedAt;
        return $this;
    }

    /**
     * Verifie si le participant a partage sa localisation
     */
    public function hasLocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Genere le lien WhatsApp pour envoyer la localisation de l'evenement
     */
    public function getWhatsappLocationLink(): ?string
    {
        if (!$this->whatsappNumber || !$this->event) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->whatsappNumber);
        $eventAddress = $this->event->getFullAddress();
        $eventTitle = $this->event->getTitle();
        $message = urlencode("Bonjour ! Voici les informations pour l'evenement \"{$eventTitle}\" :\nAdresse : {$eventAddress}");

        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * Retourne le libelle du statut
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'confirmed' => 'Confirme',
            'pending' => 'En attente',
            'cancelled' => 'Annule',
            default => 'Inconnu'
        };
    }

    /**
     * Verifie si l'inscription peut etre annulee
     */
    public function canBeCancelled(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }
        if ($this->event && !$this->event->isUpcoming()) {
            return false;
        }
        return true;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s',
            $this->user?->getFullName() ?? 'Utilisateur inconnu',
            $this->event?->getTitle() ?? 'Evenement inconnu'
        );
    }
}
