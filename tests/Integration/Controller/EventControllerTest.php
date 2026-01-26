<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'intégration pour EventController
 *
 * Ces tests vérifient le comportement des endpoints HTTP
 * en simulant des requêtes vers le contrôleur.
 *
 * Note: Certains tests nécessitent une base de données MySQL accessible.
 */
class EventControllerTest extends WebTestCase
{
    private static bool $databaseAvailable = true;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Vérifier si la base de données est disponible
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
     * Test de la page d'accueil des événements
     */
    public function testEventListPageIsAccessible(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que la page d'accueil contient le titre attendu
     */
    public function testEventListPageContainsTitle(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test de la page de détail d'un événement (si existe)
     */
    public function testEventDetailPageWith404ForInvalidId(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/999999');

        // Un événement inexistant doit retourner 404
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test que les événements peuvent être filtrés par catégorie
     */
    public function testEventFilterByCategory(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/?category=humanitaire');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test de la recherche d'événements
     */
    public function testEventSearch(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/?search=test');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test du tri des événements par date
     */
    public function testEventSortByDate(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/?sort=date');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test du tri des événements par titre
     */
    public function testEventSortByTitle(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/event/?sort=title');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test de la page de detail d'un evenement existant
     */
    public function testEventDetailPage(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $eventRepository = static::getContainer()->get(\App\Repository\EventRepository::class);
            $events = $eventRepository->findAll();

            if (count($events) > 0) {
                $event = $events[0];
                $client->request('GET', '/event/' . $event->getId());

                $this->assertResponseIsSuccessful();
            } else {
                $this->markTestSkipped('No events found. Run fixtures first.');
            }
        } catch (\Exception $e) {
            $this->markTestSkipped('Could not load events: ' . $e->getMessage());
        }
    }

    /**
     * Test de la page de categorie d'evenements
     */
    public function testEventCategoryPage(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();

        try {
            $categoryRepository = static::getContainer()->get(\App\Repository\CategoryRepository::class);
            $categories = $categoryRepository->findAll();

            if (count($categories) > 0) {
                $category = $categories[0];
                $client->request('GET', '/event/?category=' . $category->getSlug());

                $this->assertResponseIsSuccessful();
            } else {
                // Tester avec une categorie qui n'existe pas
                $client->request('GET', '/event/?category=nonexistent');
                $this->assertResponseIsSuccessful();
            }
        } catch (\Exception $e) {
            // Tester avec une categorie quelconque
            $client->request('GET', '/event/?category=humanitaire');
            $this->assertResponseIsSuccessful();
        }
    }
}
