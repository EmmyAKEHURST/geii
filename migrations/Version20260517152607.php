<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517152607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Passe toutes les FK des relations Compte/Etudiant/Enseignant/Personnel/Matiere/Entreprise en ON DELETE CASCADE (cohérence stricte).';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY `FK_81A72FA1F2C56620`');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY `FK_717E22E3F2C56620`');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY `FK_CFBDFA14DDEAB1A3`');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY `FK_CFBDFA14F46CD258`');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (num_etudiant) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_alternance DROP FOREIGN KEY `FK_6901509CA4AEAFEA`');
        $this->addSql('ALTER TABLE offre_alternance ADD CONSTRAINT FK_6901509CA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY `FK_A6BCF3DEF2C56620`');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DEF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_tuteure DROP FOREIGN KEY `FK_2ECF8F635E4BEA8B`');
        $this->addSql('ALTER TABLE projet_tuteure DROP FOREIGN KEY `FK_2ECF8F63A4AEAFEA`');
        $this->addSql('ALTER TABLE projet_tuteure ADD CONSTRAINT FK_2ECF8F635E4BEA8B FOREIGN KEY (enseignant_tuteur_id) REFERENCES enseignant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_tuteure ADD CONSTRAINT FK_2ECF8F63A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1F2C56620');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT `FK_81A72FA1F2C56620` FOREIGN KEY (compte_id) REFERENCES compte (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3F2C56620');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT `FK_717E22E3F2C56620` FOREIGN KEY (compte_id) REFERENCES compte (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14F46CD258');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14DDEAB1A3');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT `FK_CFBDFA14F46CD258` FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT `FK_CFBDFA14DDEAB1A3` FOREIGN KEY (etudiant_id) REFERENCES etudiant (num_etudiant) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE offre_alternance DROP FOREIGN KEY FK_6901509CA4AEAFEA');
        $this->addSql('ALTER TABLE offre_alternance ADD CONSTRAINT `FK_6901509CA4AEAFEA` FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DEF2C56620');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT `FK_A6BCF3DEF2C56620` FOREIGN KEY (compte_id) REFERENCES compte (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE projet_tuteure DROP FOREIGN KEY FK_2ECF8F63A4AEAFEA');
        $this->addSql('ALTER TABLE projet_tuteure DROP FOREIGN KEY FK_2ECF8F635E4BEA8B');
        $this->addSql('ALTER TABLE projet_tuteure ADD CONSTRAINT `FK_2ECF8F63A4AEAFEA` FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE projet_tuteure ADD CONSTRAINT `FK_2ECF8F635E4BEA8B` FOREIGN KEY (enseignant_tuteur_id) REFERENCES enseignant (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
