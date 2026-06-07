<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Personnel;
use App\Tests\Integration\IntegrationTestCase;

class PersonnelIntegrationTest extends IntegrationTestCase
{
    public function testPersistancePersonnelNonAdmin(): void
    {
        $personnel = new Personnel();
        $personnel->setNom('Leclerc');
        $personnel->setPrenom('Marie');
        $personnel->setFonction('Secrétaire pédagogique');
        $personnel->setAdmin(false);

        $this->em->persist($personnel);
        $this->em->flush();

        $this->assertNotNull($personnel->getId());

        $this->em->clear();

        $trouve = $this->em->find(Personnel::class, $personnel->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Leclerc', $trouve->getNom());
        $this->assertFalse($trouve->isAdmin());
    }

    public function testPersistancePersonnelAdmin(): void
    {
        $personnel = new Personnel();
        $personnel->setNom('Bernard');
        $personnel->setPrenom('Jean');
        $personnel->setFonction('Responsable administratif');
        $personnel->setAdmin(true);

        $this->em->persist($personnel);
        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(Personnel::class, $personnel->getId());
        $this->assertNotNull($trouve);
        $this->assertTrue($trouve->isAdmin());
    }
}
