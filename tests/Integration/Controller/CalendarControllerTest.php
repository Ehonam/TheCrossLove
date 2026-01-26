<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests d'integration pour CalendarController
 *
 * Ces tests verifient le comportement de la page calendrier.
 */
class CalendarControllerTest extends WebTestCase
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
     * Test que la page calendrier est accessible
     */
    public function testCalendarIsAccessible(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/calendar');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Test que la page calendrier retourne un code 200
     */
    public function testCalendarReturnsOk(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $client->request('GET', '/calendar');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test que la page calendrier contient du contenu
     */
    public function testCalendarPageHasContent(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $client = static::createClient();
        $crawler = $client->request('GET', '/calendar');

        $this->assertResponseIsSuccessful();
        // La page doit contenir quelque chose
        $this->assertGreaterThan(0, $crawler->filter('body')->count());
    }
}
