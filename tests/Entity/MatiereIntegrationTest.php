<?php

namespace App\Tests\Entity;

use App\Entity\Matiere;
use App\Tests\IntegrationTestCase;

class MatiereIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'une matière est persistée avec son nom et des collections vides.
     */
    public function testPersistanceMatiere(): void
    {
        $matiere = (new Matiere())->setNom('Systèmes embarqués');

        $this->em->persist($matiere);
        $this->em->flush();

        $this->assertNotNull($matiere->getId());

        $this->em->clear();

        $trouve = $this->em->find(Matiere::class, $matiere->getId());

        $this->assertNotNull($trouve);
        $this->assertSame('Systèmes embarqués', $trouve->getNom());
        $this->assertCount(0, $trouve->getEmploiDuTemps());
        $this->assertCount(0, $trouve->getNotes());
    }
}
