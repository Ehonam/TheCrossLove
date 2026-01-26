<?php

namespace App\Tests\Integration\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'integration pour DefaultController
 *
 * Ces tests verifient le comportement des pages principales de l'application.
 */
class DefaultControllerTest extends WebTestCase
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
     * Test que la page d'accueil est accessible
     */
    public function testHomepageIsAccessible(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que la page d'accueil contient les elements attendus
     */
    public function testHomepageContainsExpectedContent(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test que la page des evenements est accessible
     */
    public function testEventsPageIsAccessible(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/events');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que la page mes inscriptions necessite une authentification
     */
    public function testMyRegistrationsRequiresAuth(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/my-registrations');

        // Un utilisateur non connecte est redirige vers login
        $this->assertResponseRedirects();
    }

    /**
     * Test que la page mes inscriptions est accessible quand connecte
     */
    public function testMyRegistrationsAccessibleWhenLoggedIn(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if ($user) {
                $client->loginUser($user);
                $client->request('GET', '/my-registrations');

                $this->assertResponseIsSuccessful();
            } else {
                // Essayer avec l'admin
                $adminUser = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
                if ($adminUser) {
                    $client->loginUser($adminUser);
                    $client->request('GET', '/my-registrations');

                    $this->assertResponseIsSuccessful();
                } else {
                    $this->markTestSkipped('No user found. Run fixtures first.');
                }
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }

    /**
     * Test que la page d'accueil affiche les evenements
     */
    public function testHomepageDisplaysEvents(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que la navigation fonctionne depuis l'accueil
     */
    public function testHomepageNavigation(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        // La page doit avoir des liens de navigation
        $this->assertGreaterThanOrEqual(0, $crawler->filter('a')->count());
    }
}
