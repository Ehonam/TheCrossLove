<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\ToRegister;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour l'entité Event
 *
 * Ces tests vérifient le comportement des méthodes de l'entité Event
 * sans interaction avec la base de données.
 */
class EventTest extends TestCase
{
    private Event $event;

    protected function setUp(): void
    {
        $this->event = new Event();
        $this->event->setTitle('Événement Test');
        $this->event->setDescription('Une description de test pour cet événement humanitaire.');
        $this->event->setAddress('123 Rue de Test');
        $this->event->setPostalCode('75001');
        $this->event->setCity('Paris');
        $this->event->setCountry('France');
        $this->event->setOrganizer('TheCrossLove');
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));
    }

    /**
     * Test de création d'un événement
     */
    public function testEventCreation(): void
    {
        $event = new Event();

        $this->assertNull($event->getId());
        $this->assertNull($event->getTitle());
        $this->assertNull($event->getSlug());
    }

    /**
     * Test du setter/getter titre
     */
    public function testSetGetTitle(): void
    {
        $title = 'Nouvel Événement Humanitaire';
        $this->event->setTitle($title);

        $this->assertEquals($title, $this->event->getTitle());
    }

    /**
     * Test du setter/getter description
     */
    public function testSetGetDescription(): void
    {
        $description = 'Description détaillée de l\'événement humanitaire';
        $this->event->setDescription($description);

        $this->assertEquals($description, $this->event->getDescription());
    }

    /**
     * Test du setter/getter slug
     */
    public function testSetGetSlug(): void
    {
        $slug = 'evenement-test-humanitaire';
        $this->event->setSlug($slug);

        $this->assertEquals($slug, $this->event->getSlug());
    }

    /**
     * Test du setter/getter dates
     */
    public function testSetGetDates(): void
    {
        $dateStart = new \DateTime('2026-03-15 10:00:00');
        $dateEnd = new \DateTime('2026-03-15 18:00:00');

        $this->event->setDateStart($dateStart);
        $this->event->setDateEnd($dateEnd);

        $this->assertEquals($dateStart, $this->event->getDateStart());
        $this->assertEquals($dateEnd, $this->event->getDateEnd());
    }

    /**
     * Test du setter/getter adresse
     */
    public function testSetGetAddress(): void
    {
        $this->event->setAddress('456 Avenue de la Solidarité');
        $this->event->setPostalCode('69001');
        $this->event->setCity('Lyon');
        $this->event->setCountry('France');

        $this->assertEquals('456 Avenue de la Solidarité', $this->event->getAddress());
        $this->assertEquals('69001', $this->event->getPostalCode());
        $this->assertEquals('Lyon', $this->event->getCity());
        $this->assertEquals('France', $this->event->getCountry());
    }

    /**
     * Test du nombre maximum de participants
     */
    public function testSetGetMaxParticipants(): void
    {
        $this->event->setMaxParticipants(50);

        $this->assertEquals(50, $this->event->getMaxParticipants());
    }

    /**
     * Test maxParticipants null (illimité)
     */
    public function testMaxParticipantsCanBeNull(): void
    {
        $this->event->setMaxParticipants(null);

        $this->assertNull($this->event->getMaxParticipants());
    }

    /**
     * Test de la catégorie
     */
    public function testSetGetCategory(): void
    {
        $category = new Category();
        $category->setName('Humanitaire');

        $this->event->setCategory($category);

        $this->assertEquals($category, $this->event->getCategory());
        $this->assertEquals('Humanitaire', $this->event->getCategory()->getName());
    }

    /**
     * Test du créateur de l'événement
     */
    public function testSetGetCreatedBy(): void
    {
        $user = new User();
        $user->setEmail('admin@thecrosslove.com');

        $this->event->setCreatedBy($user);

        $this->assertEquals($user, $this->event->getCreatedBy());
    }

    /**
     * Test du statut de l'événement
     */
    public function testSetGetStatus(): void
    {
        $this->event->setStatus('active');
        $this->assertEquals('active', $this->event->getStatus());

        $this->event->setStatus('cancelled');
        $this->assertEquals('cancelled', $this->event->getStatus());
    }

    /**
     * Test des places disponibles avec événement vide
     */
    public function testAvailableSeatsWithNoRegistrations(): void
    {
        $this->event->setMaxParticipants(50);

        $this->assertEquals(50, $this->event->getAvailableSeats());
    }

    /**
     * Test des places disponibles sans limite
     */
    public function testAvailableSeatsUnlimited(): void
    {
        $this->event->setMaxParticipants(null);

        // Avec max null, les places sont illimitées
        $this->assertNull($this->event->getAvailableSeats());
    }

    /**
     * Test du compteur de participants
     */
    public function testParticipantCount(): void
    {
        $this->assertEquals(0, $this->event->getParticipantCount());
    }

    /**
     * Test si l'événement est complet
     */
    public function testIsFullWithNoLimit(): void
    {
        $this->event->setMaxParticipants(null);

        $this->assertFalse($this->event->isFull());
    }

    /**
     * Test si l'événement est à venir
     */
    public function testIsUpcoming(): void
    {
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));

        $this->assertTrue($this->event->isUpcoming());
    }

    /**
     * Test si l'événement est passé
     */
    public function testIsPast(): void
    {
        $this->event->setDateStart(new \DateTime('-1 week'));
        $this->event->setDateEnd(new \DateTime('-1 week +2 hours'));

        $this->assertTrue($this->event->isPast());
    }

    /**
     * Test de l'image
     */
    public function testSetGetImage(): void
    {
        $this->event->setImage('uploads/events/image.jpg');

        $this->assertEquals('uploads/events/image.jpg', $this->event->getImage());
    }

    /**
     * Test de l'organisateur
     */
    public function testSetGetOrganizer(): void
    {
        $this->event->setOrganizer('Association TheCrossLove');

        $this->assertEquals('Association TheCrossLove', $this->event->getOrganizer());
    }

    /**
     * Test des timestamps automatiques
     */
    public function testTimestamps(): void
    {
        // createdAt est initialisé dans le constructeur
        $this->assertNotNull($this->event->getCreatedAt());
        // updatedAt est défini uniquement lors d'un update via PreUpdate
        $this->assertNull($this->event->getUpdatedAt());
    }

    /**
     * Test de la collection des inscriptions
     */
    public function testRegistrationsCollection(): void
    {
        $this->assertCount(0, $this->event->getRegistrations());
    }

    /**
     * Test canRegister pour un événement actif à venir
     */
    public function testCanRegisterForActiveUpcomingEvent(): void
    {
        $this->event->setStatus('active');
        $this->event->setMaxParticipants(50);
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));

        $this->assertTrue($this->event->canRegister());
    }

    /**
     * Test isCancelled pour un événement annulé
     * Note: canRegister() ne vérifie pas le statut, seulement les dates et places
     */
    public function testIsCancelledForCancelledEvent(): void
    {
        $this->event->setStatus('cancelled');

        // isCancelled() retourne true pour un événement avec status 'cancelled'
        $this->assertTrue($this->event->isCancelled());
        // getComputedStatus() retourne 'cancelled' pour un événement annulé
        $this->assertEquals('cancelled', $this->event->getComputedStatus());
    }

    /**
     * Test canRegister pour un événement passé
     */
    public function testCannotRegisterForPastEvent(): void
    {
        $this->event->setStatus('active');
        $this->event->setDateStart(new \DateTime('-1 week'));
        $this->event->setDateEnd(new \DateTime('-1 week +2 hours'));

        $this->assertFalse($this->event->canRegister());
    }

    /**
     * Test computeSlug génère un slug à partir du titre
     */
    public function testComputeSlugGeneratesFromTitle(): void
    {
        $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
        $event = new Event();
        $event->setTitle('Mon Événement Humanitaire 2025');

        $event->computeSlug($slugger);

        $this->assertNotNull($event->getSlug());
        $this->assertStringContainsString('mon-evenement-humanitaire', $event->getSlug());
    }

    /**
     * Test computeSlug ne modifie pas un slug existant
     */
    public function testComputeSlugDoesNotOverwriteExisting(): void
    {
        $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
        $this->event->setSlug('custom-slug');

        $this->event->computeSlug($slugger);

        $this->assertEquals('custom-slug', $this->event->getSlug());
    }

    /**
     * Test getFullAddress retourne l'adresse complète formatée
     */
    public function testGetFullAddressReturnsFormattedAddress(): void
    {
        $this->event->setAddress('123 Rue de la Paix');
        $this->event->setPostalCode('75001');
        $this->event->setCity('Paris');
        $this->event->setCountry('France');

        $expected = '123 Rue de la Paix, 75001 Paris, France';
        $this->assertEquals($expected, $this->event->getFullAddress());
    }

    /**
     * Test isOngoing retourne true pour un événement en cours
     */
    public function testIsOngoingReturnsTrue(): void
    {
        $this->event->setDateStart(new \DateTime('-1 hour'));
        $this->event->setDateEnd(new \DateTime('+1 hour'));

        $this->assertTrue($this->event->isOngoing());
    }

    /**
     * Test isOngoing retourne false pour un événement à venir
     */
    public function testIsOngoingReturnsFalseForUpcoming(): void
    {
        $this->event->setDateStart(new \DateTime('+1 hour'));
        $this->event->setDateEnd(new \DateTime('+2 hours'));

        $this->assertFalse($this->event->isOngoing());
    }

    /**
     * Test isOngoing retourne false pour un événement passé
     */
    public function testIsOngoingReturnsFalseForPast(): void
    {
        $this->event->setDateStart(new \DateTime('-2 hours'));
        $this->event->setDateEnd(new \DateTime('-1 hour'));

        $this->assertFalse($this->event->isOngoing());
    }

    /**
     * Test isUserRegistered retourne true si l'utilisateur est inscrit
     */
    public function testIsUserRegisteredReturnsTrue(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $registration = new ToRegister();
        $registration->setUser($user);
        $this->event->addRegistration($registration);

        $this->assertTrue($this->event->isUserRegistered($user));
    }

    /**
     * Test isUserRegistered retourne false si l'utilisateur n'est pas inscrit
     */
    public function testIsUserRegisteredReturnsFalse(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertFalse($this->event->isUserRegistered($user));
    }

    /**
     * Test isUserRegistered retourne false pour un utilisateur null
     */
    public function testIsUserRegisteredReturnsFalseForNull(): void
    {
        $this->assertFalse($this->event->isUserRegistered(null));
    }

    /**
     * Test getDurationInHours calcule correctement la durée
     */
    public function testGetDurationInHours(): void
    {
        $this->event->setDateStart(new \DateTime('2026-03-15 10:00:00'));
        $this->event->setDateEnd(new \DateTime('2026-03-15 14:30:00'));

        // 4 heures et 30 minutes = 4.5 heures
        $this->assertEquals(4.5, $this->event->getDurationInHours());
    }

    /**
     * Test getDurationInHours pour un événement de plusieurs jours
     */
    public function testGetDurationInHoursMultipleDays(): void
    {
        $this->event->setDateStart(new \DateTime('2026-03-15 10:00:00'));
        $this->event->setDateEnd(new \DateTime('2026-03-16 10:00:00'));

        // 24 heures
        $this->assertEquals(24.0, $this->event->getDurationInHours());
    }

    /**
     * Test getStatusLabel pour un événement à venir
     */
    public function testGetStatusLabelUpcoming(): void
    {
        $this->event->setStatus('active');
        $this->event->setMaxParticipants(50);
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));

        $this->assertEquals('À venir', $this->event->getStatusLabel());
    }

    /**
     * Test getStatusLabel pour un événement annulé
     */
    public function testGetStatusLabelCancelled(): void
    {
        $this->event->setStatus('cancelled');

        $this->assertEquals('Annulé', $this->event->getStatusLabel());
    }

    /**
     * Test getStatusLabel pour un événement en cours
     */
    public function testGetStatusLabelOngoing(): void
    {
        $this->event->setStatus('active');
        $this->event->setDateStart(new \DateTime('-1 hour'));
        $this->event->setDateEnd(new \DateTime('+1 hour'));

        $this->assertEquals('En cours', $this->event->getStatusLabel());
    }

    /**
     * Test getStatusLabel pour un événement terminé
     */
    public function testGetStatusLabelPast(): void
    {
        $this->event->setStatus('active');
        $this->event->setDateStart(new \DateTime('-2 days'));
        $this->event->setDateEnd(new \DateTime('-1 day'));

        $this->assertEquals('Terminé', $this->event->getStatusLabel());
    }

    /**
     * Test d'ajout d'une inscription
     */
    public function testAddRegistration(): void
    {
        $registration = new ToRegister();

        $this->event->addRegistration($registration);

        $this->assertCount(1, $this->event->getRegistrations());
        $this->assertTrue($this->event->getRegistrations()->contains($registration));
        $this->assertSame($this->event, $registration->getEvent());
    }

    /**
     * Test de suppression d'une inscription
     */
    public function testRemoveRegistration(): void
    {
        $registration = new ToRegister();
        $this->event->addRegistration($registration);
        $this->assertCount(1, $this->event->getRegistrations());

        $this->event->removeRegistration($registration);

        $this->assertCount(0, $this->event->getRegistrations());
        $this->assertNull($registration->getEvent());
    }

    /**
     * Test que addRegistration ne duplique pas
     */
    public function testAddRegistrationDoesNotDuplicate(): void
    {
        $registration = new ToRegister();

        $this->event->addRegistration($registration);
        $this->event->addRegistration($registration);

        $this->assertCount(1, $this->event->getRegistrations());
    }

    /**
     * Test __toString retourne le titre
     */
    public function testToString(): void
    {
        $this->event->setTitle('Super Événement');

        $this->assertEquals('Super Événement', (string) $this->event);
    }

    /**
     * Test __toString retourne chaîne vide si titre null
     */
    public function testToStringReturnsEmptyWhenTitleNull(): void
    {
        $event = new Event();

        $this->assertEquals('', (string) $event);
    }
}
