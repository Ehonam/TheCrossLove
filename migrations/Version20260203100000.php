<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration: Ajouter les champs latitude et longitude à la table event
 */
final class Version20260203100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajouter les champs latitude et longitude à la table event pour la géolocalisation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event ADD latitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD longitude DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event DROP latitude');
        $this->addSql('ALTER TABLE event DROP longitude');
    }
}
