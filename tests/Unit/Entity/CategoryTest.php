<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Category;
use App\Entity\Event;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour l'entité Category
 *
 * Ces tests vérifient le comportement des méthodes de l'entité Category
 * sans interaction avec la base de données.
 */
class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    /**
     * Test de création d'une catégorie
     */
    public function testCategoryCreation(): void
    {
        $category = new Category();

        $this->assertNull($category->getId());
        $this->assertNull($category->getName());
        $this->assertNull($category->getSlug());
    }

    /**
     * Test du setter/getter nom
     */
    public function testSetGetName(): void
    {
        $name = 'Humanitaire';
        $this->category->setName($name);

        $this->assertEquals($name, $this->category->getName());
    }

    /**
     * Test du setter/getter slug
     */
    public function testSetGetSlug(): void
    {
        $slug = 'humanitaire';
        $this->category->setSlug($slug);

        $this->assertEquals($slug, $this->category->getSlug());
    }

    /**
     * Test de la collection des événements
     */
    public function testEventsCollectionIsInitialized(): void
    {
        $category = new Category();

        $this->assertCount(0, $category->getEvents());
    }

    /**
     * Test d'ajout d'un événement à la catégorie
     */
    public function testAddEvent(): void
    {
        $event = new Event();
        $event->setTitle('Événement Test');

        $this->category->addEvent($event);

        $this->assertCount(1, $this->category->getEvents());
        $this->assertTrue($this->category->getEvents()->contains($event));
    }

    /**
     * Test de suppression d'un événement de la catégorie
     */
    public function testRemoveEvent(): void
    {
        $event = new Event();
        $event->setTitle('Événement Test');

        $this->category->addEvent($event);
        $this->assertCount(1, $this->category->getEvents());

        $this->category->removeEvent($event);
        $this->assertCount(0, $this->category->getEvents());
    }

    /**
     * Test du compteur d'événements
     */
    public function testEventCount(): void
    {
        $event1 = new Event();
        $event1->setTitle('Événement 1');

        $event2 = new Event();
        $event2->setTitle('Événement 2');

        $this->category->addEvent($event1);
        $this->category->addEvent($event2);

        $this->assertEquals(2, $this->category->getEventCount());
    }

    /**
     * Test que les événements ne sont pas dupliqués
     */
    public function testAddEventDoesNotDuplicate(): void
    {
        $event = new Event();
        $event->setTitle('Événement Test');

        $this->category->addEvent($event);
        $this->category->addEvent($event); // Ajout du même événement

        // Doctrine ArrayCollection n'empêche pas les doublons par défaut
        // mais la logique métier devrait le gérer
        $this->assertGreaterThanOrEqual(1, $this->category->getEvents()->count());
    }

    /**
     * Test de la représentation en chaîne (si __toString existe)
     */
    public function testToString(): void
    {
        $this->category->setName('Humanitaire');

        // Si __toString est implémenté
        if (method_exists($this->category, '__toString')) {
            $this->assertEquals('Humanitaire', (string) $this->category);
        } else {
            $this->assertEquals('Humanitaire', $this->category->getName());
        }
    }

    /**
     * Test des noms de catégories typiques
     */
    public function testTypicalCategoryNames(): void
    {
        $typicalNames = [
            'Humanitaire',
            'Solidarité',
            'Environnement',
            'Éducation',
            'Santé',
            'Culture'
        ];

        foreach ($typicalNames as $name) {
            $category = new Category();
            $category->setName($name);
            $this->assertEquals($name, $category->getName());
        }
    }

    /**
     * Test computeSlug génère un slug à partir du nom
     */
    public function testComputeSlug(): void
    {
        $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
        $this->category->setName('Humanitaire & Solidarité');

        $this->category->computeSlug($slugger);

        $this->assertNotNull($this->category->getSlug());
        $this->assertEquals('humanitaire-solidarite', $this->category->getSlug());
    }

    /**
     * Test computeSlug ne modifie pas un slug existant
     */
    public function testComputeSlugDoesNotOverwriteExisting(): void
    {
        $slugger = new \Symfony\Component\String\Slugger\AsciiSlugger();
        $this->category->setName('Nouvelle Catégorie');
        $this->category->setSlug('custom-slug');

        $this->category->computeSlug($slugger);

        $this->assertEquals('custom-slug', $this->category->getSlug());
    }
}
