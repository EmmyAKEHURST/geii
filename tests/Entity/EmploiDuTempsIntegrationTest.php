<?php

namespace App\Tests\Entity;

use App\Entity\EmploiDuTemps;
use App\Entity\Matiere;
use App\Tests\IntegrationTestCase;

class EmploiDuTempsIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'un créneau est persisté avec sa salle et sans matière liée.
     */
    public function testPersistanceEmploiDuTemps(): void
    {
        $emploi = new EmploiDuTemps();
        $emploi->setDateHeureDebut(new \DateTime('2024-09-02 08:00:00'));
        $emploi->setDateHeureFin(new \DateTime('2024-09-02 10:00:00'));
        $emploi->setSalle('A101');

        $this->em->persist($emploi);
        $this->em->flush();

        $this->assertNotNull($emploi->getId());

        $this->em->clear();

        $trouve = $this->em->find(EmploiDuTemps::class, $emploi->getId());

        $this->assertNotNull($trouve);
        $this->assertSame('A101', $trouve->getSalle());
        $this->assertNull($trouve->getMatiere());
    }

    /**
     * Vérifie qu'un créneau peut être persisté avec une matière associée.
     */
    public function testPersistanceEmploiDuTempsAvecMatiere(): void
    {
        $matiere = new Matiere();
        $matiere->setNom('Réseaux informatiques');
        $this->em->persist($matiere);

        $emploi = new EmploiDuTemps();
        $emploi->setDateHeureDebut(new \DateTime('2024-09-03 14:00:00'));
        $emploi->setDateHeureFin(new \DateTime('2024-09-03 16:00:00'));
        $emploi->setSalle('B305');
        $emploi->setMatiere($matiere);

        $this->em->persist($emploi);
        $this->em->flush();
        $this->em->clear();

        $trouve = $this->em->find(EmploiDuTemps::class, $emploi->getId());

        $this->assertNotNull($trouve);
        $this->assertNotNull($trouve->getMatiere());
        $this->assertSame('Réseaux informatiques', $trouve->getMatiere()->getNom());
    }
}
