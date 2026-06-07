<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Entreprise;
use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;
use App\Tests\Integration\IntegrationTestCase;

class OffreAlternanceIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceOffreSansEntreprise(): void
    {
        $offre = new OffreAlternance();
        $offre->setTitre('Développeur PHP H/F');
        $offre->setDescription('Rejoignez notre équipe pour développer des applications web.');
        $offre->setDuree(12);
        $offre->setStatut(StatutAlternance::ACTIVE);

        $this->em->persist($offre);
        $this->em->flush();

        $this->assertNotNull($offre->getId());

        $this->em->clear();

        $trouve = $this->em->find(OffreAlternance::class, $offre->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Développeur PHP H/F', $trouve->getTitre());
        $this->assertSame(12, $trouve->getDuree());
        $this->assertSame(StatutAlternance::ACTIVE, $trouve->getStatut());
        $this->assertNull($trouve->getEntreprise());
    }

    public function testPersistanceOffreAvecEntreprise(): void
    {
        $entreprise = new Entreprise();
        $entreprise->setNom('WebAgency SAS');
        $entreprise->setSiret('98765432109876');
        $entreprise->setAdresse('5 avenue du Numérique, 33000 Bordeaux');
        $entreprise->setSecteur('Services numériques');
        $this->em->persist($entreprise);

        $offre = new OffreAlternance();
        $offre->setTitre('Intégrateur web H/F');
        $offre->setDescription('Intégration d\'interfaces utilisateur pour nos clients.');
        $offre->setDuree(24);
        $offre->setStatut(StatutAlternance::ACTIVE);
        $offre->setEntreprise($entreprise);

        $this->em->persist($offre);
        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(OffreAlternance::class, $offre->getId());
        $this->assertNotNull($trouve);
        $this->assertNotNull($trouve->getEntreprise());
        $this->assertSame('WebAgency SAS', $trouve->getEntreprise()->getNom());
    }

    public function testChangementStatutOffre(): void
    {
        $offre = new OffreAlternance();
        $offre->setTitre('Technicien réseau H/F');
        $offre->setDescription('Maintenance et évolution des infrastructures réseau.');
        $offre->setDuree(6);
        $offre->setStatut(StatutAlternance::ACTIVE);
        $this->em->persist($offre);
        $this->em->flush();

        $offre->setStatut(StatutAlternance::POURVUE);
        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(OffreAlternance::class, $offre->getId());
        $this->assertSame(StatutAlternance::POURVUE, $trouve->getStatut());
    }
}
