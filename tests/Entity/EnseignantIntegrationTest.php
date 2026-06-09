<?php

namespace App\Tests\Entity;

use App\Entity\Enseignant;
use App\Tests\IntegrationTestCase;

class EnseignantIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'un enseignant est persisté avec toutes ses propriétés et sans compte lié.
     */
    public function testPersistanceEnseignant(): void
    {
        $enseignant = (new Enseignant())
            ->setNom('Martin')
            ->setPrenom('Éric')
            ->setSpecialite('Électronique embarquée')
            ->setBureau('B204')
        ;

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
