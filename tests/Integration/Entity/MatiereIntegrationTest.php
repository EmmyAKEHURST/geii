<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Matiere;
use App\Tests\Integration\IntegrationTestCase;

class MatiereIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceMatiere(): void
    {
        $matiere = new Matiere();
        $matiere->setNom('Systèmes embarqués');

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
