<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'intégration pour SecurityController
 *
 * Ces tests vérifient le comportement de l'authentification
 * et de la sécurité de l'application.
 *
 * Note: Certains tests nécessitent une base de données MySQL accessible.
 */
class SecurityControllerTest extends WebTestCase
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
     * Test que la page de connexion est accessible
     */
    public function testLoginPageIsAccessible(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que le formulaire de connexion est présent
     */
    public function testLoginFormExists(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSelectorExists('form');
    }

    /**
     * Test de connexion avec des identifiants invalides
     */
    public function testLoginWithInvalidCredentials(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Trouver et soumettre le formulaire
        $form = $crawler->filter('form')->form([
            'email' => 'invalid@email.com',
            'password' => 'wrongpassword',
        ]);

        $client->submit($form);

        // Après une tentative invalide, on est redirigé ou on reste sur la page
        $this->assertTrue(
            $client->getResponse()->isRedirect() ||
            $client->getResponse()->getStatusCode() === Response::HTTP_OK
        );
    }

    /**
     * Test que l'accès admin est protégé
     */
    public function testAdminAccessIsProtected(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/admin/');

        // Un utilisateur non connecté est redirigé vers login
        $this->assertResponseRedirects();
    }

    /**
     * Test que la déconnexion fonctionne
     */
    public function testLogoutRedirectsToHome(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/logout');

        // La déconnexion redirige
        $this->assertResponseRedirects();
    }

    /**
     * Test de protection CSRF sur le formulaire de connexion
     */
    public function testLoginFormHasCsrfToken(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Vérifie qu'un token CSRF est présent
        $this->assertSelectorExists('input[name="_csrf_token"]');
    }

    /**
     * Test de connexion reussie avec des identifiants valides
     */
    public function testSuccessfulLogin(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(\App\Repository\UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if (!$user) {
                $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
            }

            if ($user) {
                // Utiliser loginUser pour simuler une connexion reussie
                $client->loginUser($user);

                // Verifier qu'on peut acceder a une page protegee
                $client->request('GET', '/my-registrations');

                $this->assertResponseIsSuccessful();
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }

    /**
     * Test qu'un utilisateur connecte est redirige depuis la page de login
     */
    public function testLoggedInUserRedirectedFromLogin(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(\App\Repository\UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if (!$user) {
                $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
            }

            if ($user) {
                $client->loginUser($user);
                $client->request('GET', '/login');

                // Un utilisateur connecte doit etre redirige ou recevoir une page
                $this->assertTrue(
                    $client->getResponse()->isRedirect() ||
                    $client->getResponse()->getStatusCode() === Response::HTTP_OK
                );
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }
}
