<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260605120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Lie Entreprise à Compte (compte_id unique, ON DELETE CASCADE).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entreprise ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA60F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60F2C56620 ON entreprise (compte_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA60F2C56620');
        $this->addSql('DROP INDEX UNIQ_D19FA60F2C56620 ON entreprise');
        $this->addSql('ALTER TABLE entreprise DROP compte_id');
    }
}
