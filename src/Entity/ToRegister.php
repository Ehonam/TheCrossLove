<?php

namespace App\Entity;

use App\Repository\ToRegisterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ToRegisterRepository::class)]
#[ORM\Table(name: 'to_register')]
#[ORM\UniqueConstraint(name: "user_event_unique", columns: ["id_user", "id_event"])]
#[UniqueEntity(
    fields: ['user', 'event'],
    message: 'Vous êtes déjà inscrit à cet événement'
)]
class ToRegister
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_registration')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registeredAt = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $status = 'confirmed';

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire")]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(name: 'id_event', referencedColumnName: 'id_event', nullable: false)]
    #[Assert\NotNull(message: "L'événement est obligatoire")]
    private ?Event $event = null;

    public function __construct()
    {
        $this->registeredAt = new \DateTime();
        $this->status = 'confirmed';
    }

    public function getId(): ?int
    {
        return $this->id;
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
        $validStatuses = ['confirmed', 'cancelled', 'waiting'];

        if ($status !== null && !in_array($status, $validStatuses, true)) {
            throw new \InvalidArgumentException(
                'Le statut doit être : confirmed, cancelled ou waiting'
            );
        }

        $this->status = $status;
        return $this;
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

    /**
     * Vérifie si l'inscription est confirmée
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Vérifie si l'inscription est annulée
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Vérifie si l'inscription est en attente
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Confirme l'inscription
     */
    public function confirm(): static
    {
        $this->status = 'confirmed';
        return $this;
    }

    /**
     * Annule l'inscription
     */
    public function cancel(): static
    {
        $this->status = 'cancelled';
        return $this;
    }

    /**
     * Met l'inscription en liste d'attente
     */
    public function setWaiting(): static
    {
        $this->status = 'waiting';
        return $this;
    }

    /**
     * Retourne le libellé du statut
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'waiting' => 'Liste d\'attente',
            default => 'Inconnu'
        };
    }

    /**
     * Retourne la couleur badge pour le statut
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'confirmed' => 'badge-success',
            'cancelled' => 'badge-danger',
            'waiting' => 'badge-warning',
            default => 'badge-secondary'
        };
    }

    /**
     * Vérifie si l'inscription peut être annulée
     * (par exemple, pas d'annulation si l'événement a déjà commencé)
     */
    public function canBeCancelled(): bool
    {
        if (!$this->event) {
            return false;
        }

        // Ne peut pas annuler si déjà annulé
        if ($this->isCancelled()) {
            return false;
        }

        // Ne peut pas annuler si l'événement est déjà commencé ou passé
        return $this->event->isUpcoming();
    }

    /**
     * Méthode magique pour l'affichage
     */
    public function __toString(): string
    {
        if ($this->user && $this->event) {
            return sprintf(
                'Inscription de %s à %s',
                $this->user->getFullName(),
                $this->event->getTitle()
            );
        }
        return 'Inscription';
    }
}
