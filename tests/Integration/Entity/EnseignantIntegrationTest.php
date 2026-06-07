<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Enseignant;
use App\Tests\Integration\IntegrationTestCase;

class EnseignantIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceEnseignant(): void
    {
        $enseignant = new Enseignant();
        $enseignant->setNom('Martin');
        $enseignant->setPrenom('Éric');
        $enseignant->setSpecialite('Électronique embarquée');
        $enseignant->setBureau('B204');

        $this->em->persist($enseignant);
        $this->em->flush();

        $this->assertNotNull($enseignant->getId());

        $this->em->clear();

        $trouve = $this->em->find(Enseignant::class, $enseignant->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Martin', $trouve->getNom());
        $this->assertSame('Éric', $trouve->getPrenom());
        $this->assertSame('Électronique embarquée', $trouve->getSpecialite());
        $this->assertSame('B204', $trouve->getBureau());
        $this->assertNull($trouve->getCompte());
    }
}
