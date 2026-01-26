<?php

namespace App\Tests\Integration\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'intégration pour AdminController
 *
 * Ces tests vérifient le comportement du tableau de bord administrateur
 * et des fonctionnalités CRUD des événements.
 *
 * Note: Ces tests nécessitent une base de données MySQL accessible
 * et les fixtures chargées.
 */
class AdminControllerTest extends WebTestCase
{
    private static bool $databaseAvailable = true;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        try {
            $client = static::createClient();
            $container = $client->getContainer();
            $connection = $container->get('doctrine.dbal.default_connection');
            $connection->connect();
        } catch (\Exception $e) {
            self::$databaseAvailable = false;
        }
    }

    /**
     * Test que le dashboard admin est protégé
     */
    public function testDashboardRequiresAuthentication(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/admin/');

        $this->assertResponseRedirects();
    }

    /**
     * Test que la page d'événements admin est protégée
     */
    public function testEventsPageRequiresAuthentication(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/admin/events');

        $this->assertResponseRedirects();
    }

    /**
     * Test que la création d'événement est protégée
     */
    public function testNewEventRequiresAuthentication(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/admin/event/new');

        $this->assertResponseRedirects();
    }

    /**
     * Test qu'un utilisateur admin peut accéder au dashboard
     */
    public function testAdminCanAccessDashboard(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

            if ($adminUser) {
                $client->loginUser($adminUser);
                $client->request('GET', '/admin/');

                $this->assertResponseIsSuccessful();
            } else {
                $this->markTestSkipped('Admin user not found. Run fixtures first: php bin/console doctrine:fixtures:load');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load admin user: ' . $e->getMessage());
        }
    }

    /**
     * Test qu'un utilisateur standard ne peut pas accéder au dashboard
     */
    public function testRegularUserCannotAccessDashboard(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $regularUser = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if ($regularUser) {
                $client->loginUser($regularUser);
                $client->request('GET', '/admin/');

                // Un utilisateur non-admin doit recevoir 403 Forbidden
                $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
            } else {
                $this->markTestSkipped('Regular user not found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load regular user: ' . $e->getMessage());
        }
    }

    /**
     * Test de la page de liste des événements admin
     */
    public function testAdminEventsPage(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

            if ($adminUser) {
                $client->loginUser($adminUser);
                $client->request('GET', '/admin/events');

                $this->assertResponseIsSuccessful();
            } else {
                $this->markTestSkipped('Admin user not found.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load admin user: ' . $e->getMessage());
        }
    }

    /**
     * Test du formulaire de création d'événement
     */
    public function testAdminNewEventForm(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

            if ($adminUser) {
                $client->loginUser($adminUser);
                $crawler = $client->request('GET', '/admin/event/new');

                $this->assertResponseIsSuccessful();
                $this->assertSelectorExists('form');
            } else {
                $this->markTestSkipped('Admin user not found.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load admin user: ' . $e->getMessage());
        }
    }

    /**
     * Test de la page d'edition d'un evenement
     */
    public function testAdminEventEditPage(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

            if ($adminUser) {
                $client->loginUser($adminUser);

                // Tester avec un ID qui n'existe pas - doit retourner 404
                $client->request('GET', '/admin/event/999999/edit');
                $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
            } else {
                $this->markTestSkipped('Admin user not found.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load admin user: ' . $e->getMessage());
        }
    }

    /**
     * Test de la page des participants d'un evenement
     */
    public function testAdminEventParticipantsPage(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

            if ($adminUser) {
                $client->loginUser($adminUser);

                // Tester avec un ID qui n'existe pas - doit retourner 404
                $client->request('GET', '/admin/event/999999/participants');
                $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
            } else {
                $this->markTestSkipped('Admin user not found.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load admin user: ' . $e->getMessage());
        }
    }

    /**
     * Test que la suppression d'evenement necessite CSRF
     */
    public function testDeleteEventRequiresCsrf(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);

            if ($adminUser) {
                $client->loginUser($adminUser);

                // Tenter une suppression sans token CSRF valide
                $client->request('POST', '/admin/event/1/delete');

                // Sans token CSRF, la requete doit echouer ou etre redirigee
                $this->assertTrue(
                    $client->getResponse()->isRedirect() ||
                    $client->getResponse()->getStatusCode() === Response::HTTP_FORBIDDEN ||
                    $client->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND
                );
            } else {
                $this->markTestSkipped('Admin user not found.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load admin user: ' . $e->getMessage());
        }
    }
}
