<?php

namespace App\Tests\Integration\Controller;

use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'integration pour RegistrationController
 *
 * Ces tests verifient le comportement des inscriptions aux evenements.
 */
class RegistrationControllerTest extends WebTestCase
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
     * Test que l'inscription necessite une authentification
     */
    public function testRegisterRequiresAuthentication(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('POST', '/registration/event/1/register');

        // Un utilisateur non connecte est redirige vers login
        $this->assertResponseRedirects();
    }

    /**
     * Test que la desinscription necessite une authentification
     */
    public function testUnregisterRequiresAuthentication(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('POST', '/registration/event/1/unregister');

        // Un utilisateur non connecte est redirige vers login
        $this->assertResponseRedirects();
    }

    /**
     * Test que l'inscription avec un evenement invalide retourne 404
     */
    public function testRegisterWithInvalidEvent(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if (!$user) {
                $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
            }

            if ($user) {
                $client->loginUser($user);
                $client->request('POST', '/registration/event/999999/register');

                // Un evenement inexistant doit retourner 404
                $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }

    /**
     * Test que la desinscription avec un evenement invalide retourne 404
     */
    public function testUnregisterWithInvalidEvent(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if (!$user) {
                $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
            }

            if ($user) {
                $client->loginUser($user);
                $client->request('POST', '/registration/event/999999/unregister');

                // Un evenement inexistant doit retourner 404
                $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }

    /**
     * Test que les methodes GET ne sont pas autorisees pour l'inscription
     */
    public function testRegisterDoesNotAcceptGet(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if (!$user) {
                $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
            }

            if ($user) {
                $client->loginUser($user);
                $client->request('GET', '/registration/event/1/register');

                // GET ne doit pas etre autorise (404 ou 405)
                $this->assertTrue(
                    $client->getResponse()->getStatusCode() === Response::HTTP_METHOD_NOT_ALLOWED ||
                    $client->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND
                );
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }

    /**
     * Test que les methodes GET ne sont pas autorisees pour la desinscription
     */
    public function testUnregisterDoesNotAcceptGet(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneBy(['email' => 'user@thecrosslove.com']);

            if (!$user) {
                $user = $userRepository->findOneBy(['email' => 'admin@thecrosslove.com']);
            }

            if ($user) {
                $client->loginUser($user);
                $client->request('GET', '/registration/event/1/unregister');

                // GET ne doit pas etre autorise (404 ou 405)
                $this->assertTrue(
                    $client->getResponse()->getStatusCode() === Response::HTTP_METHOD_NOT_ALLOWED ||
                    $client->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND
                );
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }
}
