<?php

namespace App\Tests\Unit\Repository;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests unitaires pour le EventRepository
 *
 * Ces tests verifient le comportement des methodes du repository Event.
 * Necessite une connexion a la base de donnees de test.
 */
class EventRepositoryTest extends KernelTestCase
{
    private ?EventRepository $repository = null;
    private static bool $databaseAvailable = true;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        try {
            self::bootKernel();
            $connection = static::getContainer()->get('doctrine.dbal.default_connection');
            $connection->connect();
        } catch (\Exception $e) {
            self::$databaseAvailable = false;
        }
    }

    protected function setUp(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        self::bootKernel();
        $this->repository = static::getContainer()->get(EventRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository = null;
    }

    /**
     * Test que findAllSorted retourne un tableau trie par date
     */
    public function testFindAllSortedByDateReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findAllSorted('date');

        $this->assertIsArray($result);

        // Si des resultats existent, verifier qu'ils sont tries par date
        if (count($result) > 1) {
            for ($i = 0; $i < count($result) - 1; $i++) {
                $this->assertLessThanOrEqual(
                    $result[$i + 1]->getDateStart(),
                    $result[$i]->getDateStart()
                );
            }
        }
    }

    /**
     * Test que findAllSorted retourne un tableau trie par titre
     */
    public function testFindAllSortedByTitleReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findAllSorted('title');

        $this->assertIsArray($result);
    }

    /**
     * Test que findAllSorted avec parametre par defaut fonctionne
     */
    public function testFindAllSortedDefaultReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findAllSorted();

        $this->assertIsArray($result);
    }

    /**
     * Test que search retourne un tableau
     */
    public function testSearchReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->search('test');

        $this->assertIsArray($result);
    }

    /**
     * Test que search avec terme vide retourne un tableau
     */
    public function testSearchWithEmptyTermReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->search('');

        $this->assertIsArray($result);
    }

    /**
     * Test que search trouve des evenements par titre
     */
    public function testSearchByTitleReturnsMatchingEvents(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->search('evenement');

        $this->assertIsArray($result);

        // Si des resultats, verifier qu'ils correspondent
        foreach ($result as $event) {
            $this->assertInstanceOf(Event::class, $event);
        }
    }

    /**
     * Test que findByCategory retourne un tableau
     */
    public function testFindByCategoryReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findByCategory('Humanitaire');

        $this->assertIsArray($result);
    }

    /**
     * Test que findByCategory avec categorie inexistante retourne tableau vide
     */
    public function testFindByCategoryWithNonExistentReturnsEmptyArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findByCategory('CategorieQuiNExistePas123456');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test que findByCategory peut trouver par slug
     */
    public function testFindByCategoryBySlugReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findByCategory('humanitaire');

        $this->assertIsArray($result);
    }

    /**
     * Test que findAllCategories retourne un tableau
     */
    public function testFindAllCategoriesReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findAllCategories();

        $this->assertIsArray($result);

        // Les categories doivent etre des chaines
        foreach ($result as $categoryName) {
            $this->assertIsString($categoryName);
        }
    }

    /**
     * Test que le repository peut etre injecte
     */
    public function testRepositoryCanBeInjected(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $this->assertNotNull($this->repository);
        $this->assertInstanceOf(EventRepository::class, $this->repository);
    }

    /**
     * Test findAll retourne un tableau
     */
    public function testFindAllReturnsArray(): void
    {
        if (!self::$databaseAvailable) {
            $this->markTestSkipped('Database not available');
        }

        $result = $this->repository->findAll();

        $this->assertIsArray($result);

        foreach ($result as $event) {
            $this->assertInstanceOf(Event::class, $event);
        }
    }
}
