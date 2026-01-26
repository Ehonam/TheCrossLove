<?php

namespace App\Tests\Unit\Entity;

use App\Entity\ToRegister;
use App\Entity\User;
use App\Entity\Event;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour l'entité ToRegister
 *
 * Ces tests vérifient le comportement des méthodes de l'entité ToRegister
 * sans interaction avec la base de données.
 */
class ToRegisterTest extends TestCase
{
    private ToRegister $registration;
    private User $user;
    private Event $event;

    protected function setUp(): void
    {
        $this->registration = new ToRegister();

        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setFirstName('Jean');
        $this->user->setLastName('Dupont');

        $this->event = new Event();
        $this->event->setTitle('Événement Test');
        $this->event->setDescription('Description de test pour l\'événement humanitaire.');
        $this->event->setAddress('123 Rue de Test');
        $this->event->setPostalCode('75001');
        $this->event->setCity('Paris');
        $this->event->setCountry('France');
        $this->event->setOrganizer('TheCrossLove');
        $this->event->setDateStart(new \DateTime('+1 week'));
        $this->event->setDateEnd(new \DateTime('+1 week +2 hours'));
    }

    /**
     * Test de création d'une inscription avec valeurs par défaut
     */
    public function testRegistrationCreation(): void
    {
        $registration = new ToRegister();

        $this->assertNull($registration->getId());
        $this->assertNull($registration->getUser());
        $this->assertNull($registration->getEvent());
        $this->assertNotNull($registration->getRegisteredAt());
        $this->assertEquals('confirmed', $registration->getStatus());
    }

    /**
     * Test du setter/getter User
     */
    public function testSetGetUser(): void
    {
        $this->registration->setUser($this->user);

        $this->assertSame($this->user, $this->registration->getUser());
    }

    /**
     * Test du setter/getter Event
     */
    public function testSetGetEvent(): void
    {
        $this->registration->setEvent($this->event);

        $this->assertSame($this->event, $this->registration->getEvent());
    }

    /**
     * Test du setter/getter RegisteredAt
     */
    public function testSetGetRegisteredAt(): void
    {
        $date = new \DateTime('2026-03-15 10:00:00');
        $this->registration->setRegisteredAt($date);

        $this->assertEquals($date, $this->registration->getRegisteredAt());
    }

    /**
     * Test que registeredAt est défini automatiquement
     */
    public function testRegisteredAtIsSetAutomatically(): void
    {
        $registration = new ToRegister();
        $now = new \DateTime();

        $this->assertInstanceOf(\DateTimeInterface::class, $registration->getRegisteredAt());

        // La date doit être proche de maintenant (dans les 5 secondes)
        $diff = $now->getTimestamp() - $registration->getRegisteredAt()->getTimestamp();
        $this->assertLessThan(5, abs($diff));
    }

    /**
     * Test du setter/getter Status
     */
    public function testSetGetStatus(): void
    {
        $this->registration->setStatus('pending');
        $this->assertEquals('pending', $this->registration->getStatus());

        $this->registration->setStatus('cancelled');
        $this->assertEquals('cancelled', $this->registration->getStatus());

        $this->registration->setStatus('confirmed');
        $this->assertEquals('confirmed', $this->registration->getStatus());
    }

    /**
     * Test du statut par défaut
     */
    public function testDefaultStatus(): void
    {
        $registration = new ToRegister();

        $this->assertEquals('confirmed', $registration->getStatus());
    }

    /**
     * Test du setter/getter WhatsappNumber
     */
    public function testSetGetWhatsappNumber(): void
    {
        $this->registration->setWhatsappNumber('+33612345678');

        $this->assertEquals('+33612345678', $this->registration->getWhatsappNumber());
    }

    /**
     * Test du setter/getter Latitude
     */
    public function testSetGetLatitude(): void
    {
        $this->registration->setLatitude('48.8566969');

        $this->assertEquals('48.8566969', $this->registration->getLatitude());
    }

    /**
     * Test du setter/getter Longitude
     */
    public function testSetGetLongitude(): void
    {
        $this->registration->setLongitude('2.3514616');

        $this->assertEquals('2.3514616', $this->registration->getLongitude());
    }

    /**
     * Test du setter/getter LocationUpdatedAt
     */
    public function testSetGetLocationUpdatedAt(): void
    {
        $date = new \DateTime('2026-03-15 14:30:00');
        $this->registration->setLocationUpdatedAt($date);

        $this->assertEquals($date, $this->registration->getLocationUpdatedAt());
    }

    /**
     * Test hasLocation retourne true quand les coordonnées sont définies
     */
    public function testHasLocationReturnsTrue(): void
    {
        $this->registration->setLatitude('48.8566969');
        $this->registration->setLongitude('2.3514616');

        $this->assertTrue($this->registration->hasLocation());
    }

    /**
     * Test hasLocation retourne false quand latitude manque
     */
    public function testHasLocationReturnsFalseWithoutLatitude(): void
    {
        $this->registration->setLongitude('2.3514616');

        $this->assertFalse($this->registration->hasLocation());
    }

    /**
     * Test hasLocation retourne false quand longitude manque
     */
    public function testHasLocationReturnsFalseWithoutLongitude(): void
    {
        $this->registration->setLatitude('48.8566969');

        $this->assertFalse($this->registration->hasLocation());
    }

    /**
     * Test hasLocation retourne false quand aucune coordonnée
     */
    public function testHasLocationReturnsFalseWithoutCoordinates(): void
    {
        $this->assertFalse($this->registration->hasLocation());
    }

    /**
     * Test getWhatsappLocationLink génère un lien valide
     */
    public function testGetWhatsappLocationLink(): void
    {
        $this->registration->setWhatsappNumber('+33612345678');
        $this->registration->setEvent($this->event);

        $link = $this->registration->getWhatsappLocationLink();

        $this->assertNotNull($link);
        $this->assertStringStartsWith('https://wa.me/', $link);
        $this->assertStringContainsString('33612345678', $link);
        $this->assertStringContainsString('text=', $link);
    }

    /**
     * Test getWhatsappLocationLink retourne null sans numéro WhatsApp
     */
    public function testGetWhatsappLocationLinkReturnsNullWithoutPhone(): void
    {
        $this->registration->setEvent($this->event);

        $this->assertNull($this->registration->getWhatsappLocationLink());
    }

    /**
     * Test getWhatsappLocationLink retourne null sans événement
     */
    public function testGetWhatsappLocationLinkReturnsNullWithoutEvent(): void
    {
        $this->registration->setWhatsappNumber('+33612345678');

        $this->assertNull($this->registration->getWhatsappLocationLink());
    }

    /**
     * Test getStatusLabel pour confirmed
     */
    public function testGetStatusLabelConfirmed(): void
    {
        $this->registration->setStatus('confirmed');

        $this->assertEquals('Confirme', $this->registration->getStatusLabel());
    }

    /**
     * Test getStatusLabel pour pending
     */
    public function testGetStatusLabelPending(): void
    {
        $this->registration->setStatus('pending');

        $this->assertEquals('En attente', $this->registration->getStatusLabel());
    }

    /**
     * Test getStatusLabel pour cancelled
     */
    public function testGetStatusLabelCancelled(): void
    {
        $this->registration->setStatus('cancelled');

        $this->assertEquals('Annule', $this->registration->getStatusLabel());
    }

    /**
     * Test getStatusLabel pour statut inconnu
     */
    public function testGetStatusLabelUnknown(): void
    {
        $this->registration->setStatus('unknown_status');

        $this->assertEquals('Inconnu', $this->registration->getStatusLabel());
    }

    /**
     * Test canBeCancelled retourne true pour inscription confirmée avec événement à venir
     */
    public function testCanBeCancelledReturnsTrueForConfirmedUpcoming(): void
    {
        $this->registration->setStatus('confirmed');
        $this->registration->setEvent($this->event);

        $this->assertTrue($this->registration->canBeCancelled());
    }

    /**
     * Test canBeCancelled retourne true pour inscription pending avec événement à venir
     */
    public function testCanBeCancelledReturnsTrueForPendingUpcoming(): void
    {
        $this->registration->setStatus('pending');
        $this->registration->setEvent($this->event);

        $this->assertTrue($this->registration->canBeCancelled());
    }

    /**
     * Test canBeCancelled retourne false pour inscription déjà annulée
     */
    public function testCanBeCancelledReturnsFalseForCancelled(): void
    {
        $this->registration->setStatus('cancelled');
        $this->registration->setEvent($this->event);

        $this->assertFalse($this->registration->canBeCancelled());
    }

    /**
     * Test canBeCancelled retourne false pour événement passé
     */
    public function testCanBeCancelledReturnsFalseForPastEvent(): void
    {
        $pastEvent = new Event();
        $pastEvent->setTitle('Événement Passé');
        $pastEvent->setDescription('Description de l\'événement passé.');
        $pastEvent->setAddress('456 Rue Passée');
        $pastEvent->setPostalCode('75002');
        $pastEvent->setCity('Paris');
        $pastEvent->setCountry('France');
        $pastEvent->setOrganizer('TheCrossLove');
        $pastEvent->setDateStart(new \DateTime('-1 week'));
        $pastEvent->setDateEnd(new \DateTime('-1 week +2 hours'));

        $this->registration->setStatus('confirmed');
        $this->registration->setEvent($pastEvent);

        $this->assertFalse($this->registration->canBeCancelled());
    }

    /**
     * Test canBeCancelled retourne true si pas d'événement associé
     */
    public function testCanBeCancelledReturnsTrueWithoutEvent(): void
    {
        $this->registration->setStatus('confirmed');

        $this->assertTrue($this->registration->canBeCancelled());
    }

    /**
     * Test __toString retourne le format attendu
     */
    public function testToString(): void
    {
        $this->registration->setUser($this->user);
        $this->registration->setEvent($this->event);

        $expected = 'Jean Dupont - Événement Test';
        $this->assertEquals($expected, (string) $this->registration);
        $this->assertEquals($expected, $this->registration->__toString());
    }

    /**
     * Test __toString avec utilisateur null
     */
    public function testToStringWithNullUser(): void
    {
        $this->registration->setEvent($this->event);

        $result = (string) $this->registration;
        $this->assertStringContainsString('Utilisateur inconnu', $result);
        $this->assertStringContainsString('Événement Test', $result);
    }

    /**
     * Test __toString avec événement null
     */
    public function testToStringWithNullEvent(): void
    {
        $this->registration->setUser($this->user);

        $result = (string) $this->registration;
        $this->assertStringContainsString('Jean Dupont', $result);
        $this->assertStringContainsString('Evenement inconnu', $result);
    }

    /**
     * Test fluent interface pour setUser
     */
    public function testSetUserReturnsSelf(): void
    {
        $result = $this->registration->setUser($this->user);

        $this->assertSame($this->registration, $result);
    }

    /**
     * Test fluent interface pour setEvent
     */
    public function testSetEventReturnsSelf(): void
    {
        $result = $this->registration->setEvent($this->event);

        $this->assertSame($this->registration, $result);
    }

    /**
     * Test fluent interface pour setStatus
     */
    public function testSetStatusReturnsSelf(): void
    {
        $result = $this->registration->setStatus('pending');

        $this->assertSame($this->registration, $result);
    }
}
