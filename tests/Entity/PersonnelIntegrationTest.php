<?php

namespace App\Tests\Entity;

use App\Entity\Personnel;
use App\Tests\IntegrationTestCase;

class PersonnelIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'un membre du personnel non-administrateur est persisté correctement.
     */
    public function testPersistancePersonnelNonAdmin(): void
    {
        $personnel = (new Personnel())
            ->setNom('Leclerc')
            ->setPrenom('Marie')
            ->setFonction('Secrétaire pédagogique')
            ->setAdmin(false)
        ;

        $this->em->persist($personnel);
        $this->em->flush();

        $this->assertNotNull($personnel->getId());

        $this->em->clear();

        $trouve = $this->em->find(Personnel::class, $personnel->getId());

        $this->assertNotNull($trouve);
        $this->assertSame('Leclerc', $trouve->getNom());
        $this->assertFalse($trouve->isAdmin());
    }

    /**
     * Vérifie qu'un membre du personnel administrateur est persisté avec le flag admin à true.
     */
    public function testPersistancePersonnelAdmin(): void
    {
        $personnel = (new Personnel())
            ->setNom('Bernard')
            ->setPrenom('Jean')
            ->setFonction('Responsable administratif')
            ->setAdmin(true)
        ;

        $this->em->persist($personnel);
        $this->em->flush();
        $this->em->clear();

        $trouve = $this->em->find(Personnel::class, $personnel->getId());

        $this->assertNotNull($trouve);
        $this->assertTrue($trouve->isAdmin());
    }
}
