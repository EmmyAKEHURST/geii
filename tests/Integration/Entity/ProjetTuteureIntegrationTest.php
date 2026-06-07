<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Enseignant;
use App\Entity\Entreprise;
use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
use App\Tests\Integration\IntegrationTestCase;

class ProjetTuteureIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceProjetSansRelations(): void
    {
        $projet = new ProjetTuteure();
        $projet->setTitre('Application de gestion de stock');
        $projet->setDescription('Développement d\'une application web de gestion de stock pour PME.');
        $projet->setAnnee(2024);
        $projet->setStatut(StatutProjetTuteure::OUVERT);

        $this->em->persist($projet);
        $this->em->flush();

        $this->assertNotNull($projet->getId());

        $this->em->clear();

        $trouve = $this->em->find(ProjetTuteure::class, $projet->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Application de gestion de stock', $trouve->getTitre());
        $this->assertSame(2024, $trouve->getAnnee());
        $this->assertSame(StatutProjetTuteure::OUVERT, $trouve->getStatut());
        $this->assertNull($trouve->getEntreprise());
        $this->assertNull($trouve->getEnseignantTuteur());
    }

    public function testPersistanceProjetAvecRelations(): void
    {
        $entreprise = new Entreprise();
        $entreprise->setNom('IndustrialTech SA');
        $entreprise->setSiret('11223344556677');
        $entreprise->setAdresse('Zone industrielle Nord, 59000 Lille');
        $entreprise->setSecteur('Industrie');
        $this->em->persist($entreprise);

        $enseignant = new Enseignant();
        $enseignant->setNom('Dupuis');
        $enseignant->setPrenom('Paul');
        $enseignant->setSpecialite('Génie industriel');
        $enseignant->setBureau('C102');
        $this->em->persist($enseignant);

        $projet = new ProjetTuteure();
        $projet->setTitre('Automatisation d\'une ligne de production');
        $projet->setDescription('Étude et mise en œuvre de l\'automatisation d\'une ligne de production.');
        $projet->setAnnee(2024);
        $projet->setStatut(StatutProjetTuteure::EN_COURS);
        $projet->setEntreprise($entreprise);
        $projet->setEnseignantTuteur($enseignant);

        $this->em->persist($projet);
        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(ProjetTuteure::class, $projet->getId());
        $this->assertNotNull($trouve);
        $this->assertSame(StatutProjetTuteure::EN_COURS, $trouve->getStatut());
        $this->assertNotNull($trouve->getEntreprise());
        $this->assertSame('IndustrialTech SA', $trouve->getEntreprise()->getNom());
        $this->assertNotNull($trouve->getEnseignantTuteur());
        $this->assertSame('Dupuis', $trouve->getEnseignantTuteur()->getNom());
    }
}
