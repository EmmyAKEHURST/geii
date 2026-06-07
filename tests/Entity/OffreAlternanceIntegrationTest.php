<?php

namespace App\Tests\Entity;

use App\Entity\Entreprise;
use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;
use App\Tests\IntegrationTestCase;

class OffreAlternanceIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'une offre d'alternance est persistée sans entreprise liée.
     */
    public function testPersistanceOffreSansEntreprise(): void
    {
        $offre = (new OffreAlternance())
            ->setTitre('Développeur PHP H/F')
            ->setDescription('Rejoignez notre équipe pour développer des applications web.')
            ->setDuree(12)
            ->setStatut(StatutAlternance::ACTIVE)
        ;

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

    /**
     * Vérifie qu'une offre d'alternance est persistée avec son entreprise.
     */
    public function testPersistanceOffreAvecEntreprise(): void
    {
        $entreprise = (new Entreprise())
            ->setNom('WebAgency SAS')
            ->setSiret('98765432109876')
            ->setAdresse('5 avenue du Numérique, 33000 Bordeaux')
            ->setSecteur('Services numériques')
        ;

        $this->em->persist($entreprise);

        $offre = (new OffreAlternance())
            ->setTitre('Intégrateur web H/F')
            ->setDescription('Intégration d\'interfaces utilisateur pour nos clients.')
            ->setDuree(24)
            ->setStatut(StatutAlternance::ACTIVE)
            ->setEntreprise($entreprise)
        ;

        $this->em->persist($offre);
        $this->em->flush();
        $this->em->clear();

        $trouve = $this->em->find(OffreAlternance::class, $offre->getId());

        $this->assertNotNull($trouve);
        $this->assertNotNull($trouve->getEntreprise());
        $this->assertSame('WebAgency SAS', $trouve->getEntreprise()->getNom());
    }

    /**
     * Vérifie que le statut d'une offre peut être modifié et persisté.
     */
    public function testChangementStatutOffre(): void
    {
        $offre = (new OffreAlternance())
            ->setTitre('Technicien réseau H/F')
            ->setDescription('Maintenance et évolution des infrastructures réseau.')
            ->setDuree(6)
            ->setStatut(StatutAlternance::ACTIVE)
        ;

        $this->em->persist($offre);
        $this->em->flush();

        $offre->setStatut(StatutAlternance::POURVUE);

        $this->em->flush();
        $this->em->clear();

        $trouve = $this->em->find(OffreAlternance::class, $offre->getId());
        $this->assertSame(StatutAlternance::POURVUE, $trouve->getStatut());
    }
}
