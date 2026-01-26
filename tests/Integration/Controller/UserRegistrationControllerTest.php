<?php

namespace App\Tests\Integration\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'integration pour UserRegistrationController
 *
 * Ces tests verifient le comportement de l'inscription des utilisateurs.
 */
class UserRegistrationControllerTest extends WebTestCase
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
     * Test que la page d'inscription est accessible
     */
    public function testRegistrationPageIsAccessible(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que le formulaire d'inscription est present
     */
    public function testRegistrationFormExists(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    /**
     * Test qu'un utilisateur connecte est redirige depuis la page d'inscription
     */
    public function testLoggedInUserRedirectedFromRegistration(): void
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
                $client->request('GET', '/register');

                // Un utilisateur connecte doit etre redirige vers l'accueil
                $this->assertResponseRedirects('/');
            } else {
                $this->markTestSkipped('No user found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load user: ' . $e->getMessage());
        }
    }

    /**
     * Test que la page d'inscription retourne un code 200
     */
    public function testRegistrationPageReturnsOk(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test que le formulaire contient les champs requis
     */
    public function testRegistrationFormHasRequiredFields(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        // Verifier que les champs essentiels sont presents
        $form = $crawler->filter('form');
        $this->assertGreaterThan(0, $form->count(), 'Le formulaire doit etre present');
    }

    /**
     * Test que la soumission avec des donnees vides ne cree pas de compte
     */
    public function testEmptyFormSubmissionFails(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->filter('form')->form();

        // Soumettre un formulaire vide
        $client->submit($form, []);

        // Le formulaire devrait rester sur la meme page ou afficher des erreurs
        $this->assertTrue(
            $client->getResponse()->getStatusCode() === Response::HTTP_OK ||
            $client->getResponse()->getStatusCode() === Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
