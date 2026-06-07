<?php

namespace App\Tests\Entity;

use App\Entity\Enseignant;
use App\Entity\Entreprise;
use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
use App\Tests\IntegrationTestCase;

class ProjetTuteureIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'un projet tuteuré est persisté sans entreprise ni enseignant tuteur.
     */
    public function testPersistanceProjetSansRelations(): void
    {
        $projet = (new ProjetTuteure())
            ->setTitre('Application de gestion de stock')
            ->setDescription('Développement d\'une application web de gestion de stock pour PME.')
            ->setAnnee(2024)
            ->setStatut(StatutProjetTuteure::OUVERT)
        ;

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

    /**
     * Vérifie qu'un projet tuteuré est persisté avec une entreprise et un enseignant tuteur.
     */
    public function testPersistanceProjetAvecRelations(): void
    {
        $entreprise = (new Entreprise())
            ->setNom('IndustrialTech SA')
            ->setSiret('11223344556677')
            ->setAdresse('Zone industrielle Nord, 59000 Lille')
            ->setSecteur('Industrie')
        ;

        $this->em->persist($entreprise);

        $enseignant = (new Enseignant())
            ->setNom('Dupuis')
            ->setPrenom('Paul')
            ->setSpecialite('Génie industriel')
            ->setBureau('C102')
        ;

        $this->em->persist($enseignant);

        $projet = (new ProjetTuteure())
            ->setTitre('Automatisation d\'une ligne de production')
            ->setDescription('Étude et mise en œuvre de l\'automatisation d\'une ligne de production.')
            ->setAnnee(2024)
            ->setStatut(StatutProjetTuteure::EN_COURS)
            ->setEntreprise($entreprise)
            ->setEnseignantTuteur($enseignant)
        ;

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
