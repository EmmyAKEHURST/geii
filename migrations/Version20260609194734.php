<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260609194734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne publie sur ProjetTuteure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE projet_tuteure ADD COLUMN publie BOOLEAN NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__projet_tuteure AS SELECT id, titre, description, annee, statut, entreprise_id, enseignant_tuteur_id FROM projet_tuteure');
        $this->addSql('DROP TABLE projet_tuteure');
        $this->addSql('CREATE TABLE projet_tuteure (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description CLOB NOT NULL, annee INTEGER NOT NULL, statut VARCHAR(255) NOT NULL, entreprise_id INTEGER DEFAULT NULL, enseignant_tuteur_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO projet_tuteure (id, titre, description, annee, statut, entreprise_id, enseignant_tuteur_id) SELECT id, titre, description, annee, statut, entreprise_id, enseignant_tuteur_id FROM __temp__projet_tuteure');
        $this->addSql('DROP TABLE __temp__projet_tuteure');
    }
}
