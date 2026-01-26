<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour l'entité User
 *
 * Ces tests vérifient le comportement des méthodes de l'entité User
 * sans interaction avec la base de données.
 */
class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    /**
     * Test de création d'un utilisateur avec valeurs par défaut
     */
    public function testUserCreation(): void
    {
        $user = new User();

        $this->assertNull($user->getId());
        $this->assertNull($user->getEmail());
        // getRoles() retourne toujours au moins ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertNotNull($user->getCreationUser());
    }

    /**
     * Test du setter/getter email
     */
    public function testSetGetEmail(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);

        $this->assertEquals($email, $this->user->getEmail());
        $this->assertEquals($email, $this->user->getUserIdentifier());
    }

    /**
     * Test du setter/getter prénom
     */
    public function testSetGetFirstName(): void
    {
        $firstName = 'Jean';
        $this->user->setFirstName($firstName);

        $this->assertEquals($firstName, $this->user->getFirstName());
    }

    /**
     * Test du setter/getter nom
     */
    public function testSetGetLastName(): void
    {
        $lastName = 'Dupont';
        $this->user->setLastName($lastName);

        $this->assertEquals($lastName, $this->user->getLastName());
    }

    /**
     * Test des rôles par défaut
     */
    public function testDefaultRoles(): void
    {
        // Par défaut, getRoles() doit retourner au moins ROLE_USER
        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_USER', $roles);
    }

    /**
     * Test d'ajout de rôle ADMIN
     */
    public function testSetRolesWithAdmin(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // ROLE_USER est toujours présent
    }

    /**
     * Test d'ajout de rôle COORDINATOR
     */
    public function testSetRolesWithCoordinator(): void
    {
        $this->user->setRoles(['ROLE_COORDINATOR']);
        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_COORDINATOR', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    /**
     * Test du setter/getter password
     */
    public function testSetGetPassword(): void
    {
        $password = 'hashed_password_123';
        $this->user->setPassword($password);

        $this->assertEquals($password, $this->user->getPassword());
    }

    /**
     * Test de la méthode eraseCredentials
     */
    public function testEraseCredentials(): void
    {
        // Cette méthode ne doit pas lever d'exception
        $this->user->eraseCredentials();
        $this->assertTrue(true);
    }

    /**
     * Test de la date de création automatique
     */
    public function testCreationDateIsSetAutomatically(): void
    {
        $user = new User();
        $now = new \DateTime();

        $this->assertInstanceOf(\DateTimeInterface::class, $user->getCreationUser());

        // La date de création doit être proche de maintenant (dans les 5 secondes)
        $diff = $now->getTimestamp() - $user->getCreationUser()->getTimestamp();
        $this->assertLessThan(5, abs($diff));
    }

    /**
     * Test de la collection des événements créés
     */
    public function testEventsCollectionIsInitialized(): void
    {
        $user = new User();

        $this->assertCount(0, $user->getEvents());
    }

    /**
     * Test de la collection des inscriptions
     */
    public function testRegistrationsCollectionIsInitialized(): void
    {
        $user = new User();

        $this->assertCount(0, $user->getRegistrations());
    }

    /**
     * Test du nom complet (si méthode existe)
     */
    public function testGetFullName(): void
    {
        $this->user->setFirstName('Jean');
        $this->user->setLastName('Dupont');

        // Vérifie que les deux valeurs sont bien définies
        $this->assertEquals('Jean', $this->user->getFirstName());
        $this->assertEquals('Dupont', $this->user->getLastName());
    }

    /**
     * Test de l'unicité des rôles
     */
    public function testRolesAreUnique(): void
    {
        $this->user->setRoles(['ROLE_ADMIN', 'ROLE_ADMIN', 'ROLE_USER']);
        $roles = $this->user->getRoles();

        // Vérifie qu'il n'y a pas de doublons
        $this->assertEquals(count($roles), count(array_unique($roles)));
    }

    /**
     * Test d'ajout de rôle via addRole()
     */
    public function testAddRole(): void
    {
        $this->user->addRole('ROLE_COORDINATOR');
        $roles = $this->user->getRoles();

        $this->assertContains('ROLE_COORDINATOR', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    /**
     * Test que addRole() ne duplique pas les rôles
     */
    public function testAddRoleDoesNotDuplicate(): void
    {
        $this->user->addRole('ROLE_ADMIN');
        $this->user->addRole('ROLE_ADMIN');
        $roles = $this->user->getRoles();

        // Compte le nombre d'occurrences de ROLE_ADMIN
        $adminCount = array_count_values($roles)['ROLE_ADMIN'] ?? 0;
        $this->assertEquals(1, $adminCount);
    }

    /**
     * Test de suppression de rôle via removeRole()
     */
    public function testRemoveRole(): void
    {
        $this->user->setRoles(['ROLE_ADMIN', 'ROLE_COORDINATOR']);
        $this->user->removeRole('ROLE_ADMIN');
        $roles = $this->user->getRoles();

        $this->assertNotContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_COORDINATOR', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }

    /**
     * Test de la méthode hasRole() retourne true
     */
    public function testHasRoleReturnsTrue(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);

        $this->assertTrue($this->user->hasRole('ROLE_ADMIN'));
        $this->assertTrue($this->user->hasRole('ROLE_USER')); // Toujours présent
    }

    /**
     * Test de la méthode hasRole() retourne false
     */
    public function testHasRoleReturnsFalse(): void
    {
        $this->user->setRoles([]);

        $this->assertFalse($this->user->hasRole('ROLE_ADMIN'));
        $this->assertFalse($this->user->hasRole('ROLE_COORDINATOR'));
    }

    /**
     * Test isAdmin() retourne true pour un admin
     */
    public function testIsAdminReturnsTrue(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);

        $this->assertTrue($this->user->isAdmin());
    }

    /**
     * Test isAdmin() retourne false pour un utilisateur normal
     */
    public function testIsAdminReturnsFalse(): void
    {
        $this->user->setRoles([]);

        $this->assertFalse($this->user->isAdmin());
    }

    /**
     * Test d'ajout d'un événement
     */
    public function testAddEvent(): void
    {
        $event = new \App\Entity\Event();
        $event->setTitle('Événement Test');

        $this->user->addEvent($event);

        $this->assertCount(1, $this->user->getEvents());
        $this->assertTrue($this->user->getEvents()->contains($event));
        $this->assertSame($this->user, $event->getCreatedBy());
    }

    /**
     * Test de suppression d'un événement
     */
    public function testRemoveEvent(): void
    {
        $event = new \App\Entity\Event();
        $event->setTitle('Événement Test');

        $this->user->addEvent($event);
        $this->assertCount(1, $this->user->getEvents());

        $this->user->removeEvent($event);
        $this->assertCount(0, $this->user->getEvents());
        $this->assertNull($event->getCreatedBy());
    }

    /**
     * Test d'ajout d'une inscription
     */
    public function testAddRegistration(): void
    {
        $registration = new \App\Entity\ToRegister();

        $this->user->addRegistration($registration);

        $this->assertCount(1, $this->user->getRegistrations());
        $this->assertTrue($this->user->getRegistrations()->contains($registration));
        $this->assertSame($this->user, $registration->getUser());
    }

    /**
     * Test de suppression d'une inscription
     */
    public function testRemoveRegistration(): void
    {
        $registration = new \App\Entity\ToRegister();

        $this->user->addRegistration($registration);
        $this->assertCount(1, $this->user->getRegistrations());

        $this->user->removeRegistration($registration);
        $this->assertCount(0, $this->user->getRegistrations());
        $this->assertNull($registration->getUser());
    }

    /**
     * Test de la méthode __toString()
     */
    public function testToString(): void
    {
        $this->user->setFirstName('Jean');
        $this->user->setLastName('Dupont');

        $this->assertEquals('Jean Dupont', (string) $this->user);
        $this->assertEquals('Jean Dupont', $this->user->__toString());
    }

    /**
     * Test getFullName() avec les valeurs définies
     */
    public function testGetFullNameReturnsCorrectFormat(): void
    {
        $this->user->setFirstName('Marie');
        $this->user->setLastName('Martin');

        $this->assertEquals('Marie Martin', $this->user->getFullName());
    }
}
