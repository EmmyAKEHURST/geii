<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517151745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Relations : Compte<->Etudiant/Enseignant/Personnel, Note<->Etudiant+Matiere, OffreAlternance->Entreprise, ProjetTuteure->Entreprise+Enseignant';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81A72FA1F2C56620 ON enseignant (compte_id)');
        $this->addSql('ALTER TABLE etudiant ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_717E22E3F2C56620 ON etudiant (compte_id)');
        $this->addSql('ALTER TABLE note ADD matiere_id INT DEFAULT NULL, ADD etudiant_id VARCHAR(255) DEFAULT NULL, DROP matiere, CHANGE commentaire commentaire LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (num_etudiant) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_CFBDFA14F46CD258 ON note (matiere_id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14DDEAB1A3 ON note (etudiant_id)');
        $this->addSql('ALTER TABLE offre_alternance ADD entreprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre_alternance ADD CONSTRAINT FK_6901509CA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6901509CA4AEAFEA ON offre_alternance (entreprise_id)');
        $this->addSql('ALTER TABLE personnel ADD compte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE personnel ADD CONSTRAINT FK_A6BCF3DEF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A6BCF3DEF2C56620 ON personnel (compte_id)');
        $this->addSql('ALTER TABLE projet_tuteure ADD entreprise_id INT DEFAULT NULL, ADD enseignant_tuteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet_tuteure ADD CONSTRAINT FK_2ECF8F63A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE projet_tuteure ADD CONSTRAINT FK_2ECF8F635E4BEA8B FOREIGN KEY (enseignant_tuteur_id) REFERENCES enseignant (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2ECF8F63A4AEAFEA ON projet_tuteure (entreprise_id)');
        $this->addSql('CREATE INDEX IDX_2ECF8F635E4BEA8B ON projet_tuteure (enseignant_tuteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1F2C56620');
        $this->addSql('DROP INDEX UNIQ_81A72FA1F2C56620 ON enseignant');
        $this->addSql('ALTER TABLE enseignant DROP compte_id');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3F2C56620');
        $this->addSql('DROP INDEX UNIQ_717E22E3F2C56620 ON etudiant');
        $this->addSql('ALTER TABLE etudiant DROP compte_id');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14F46CD258');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14DDEAB1A3');
        $this->addSql('DROP INDEX IDX_CFBDFA14F46CD258 ON note');
        $this->addSql('DROP INDEX IDX_CFBDFA14DDEAB1A3 ON note');
        $this->addSql('ALTER TABLE note ADD matiere VARCHAR(255) NOT NULL, DROP matiere_id, DROP etudiant_id, CHANGE commentaire commentaire LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE offre_alternance DROP FOREIGN KEY FK_6901509CA4AEAFEA');
        $this->addSql('DROP INDEX IDX_6901509CA4AEAFEA ON offre_alternance');
        $this->addSql('ALTER TABLE offre_alternance DROP entreprise_id');
        $this->addSql('ALTER TABLE personnel DROP FOREIGN KEY FK_A6BCF3DEF2C56620');
        $this->addSql('DROP INDEX UNIQ_A6BCF3DEF2C56620 ON personnel');
        $this->addSql('ALTER TABLE personnel DROP compte_id');
        $this->addSql('ALTER TABLE projet_tuteure DROP FOREIGN KEY FK_2ECF8F63A4AEAFEA');
        $this->addSql('ALTER TABLE projet_tuteure DROP FOREIGN KEY FK_2ECF8F635E4BEA8B');
        $this->addSql('DROP INDEX IDX_2ECF8F63A4AEAFEA ON projet_tuteure');
        $this->addSql('DROP INDEX IDX_2ECF8F635E4BEA8B ON projet_tuteure');
        $this->addSql('ALTER TABLE projet_tuteure DROP entreprise_id, DROP enseignant_tuteur_id');
    }
}
