<?php

namespace App\Tests\Integration;

/**
 * Trait pour les tests nécessitant une base de données
 *
 * Ce trait permet de vérifier si la base de données est disponible
 * et de marquer les tests comme "skipped" si elle ne l'est pas.
 */
trait DatabaseTestTrait
{
    /**
     * Vérifie si la base de données est disponible
     */
    protected function skipIfDatabaseNotAvailable(): void
    {
        try {
            $container = static::getContainer();
            $connection = $container->get('doctrine.dbal.default_connection');
            $connection->connect();
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }
}
