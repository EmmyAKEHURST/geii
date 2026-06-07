<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Entreprise;
use App\Tests\Integration\IntegrationTestCase;

class EntrepriseIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceEntreprise(): void
    {
        $entreprise = new Entreprise();
        $entreprise->setNom('TechCorp SARL');
        $entreprise->setSiret('12345678901234');
        $entreprise->setAdresse('12 rue de la Paix, 75001 Paris');
        $entreprise->setSecteur('Informatique');

        $this->em->persist($entreprise);
        $this->em->flush();

        $this->assertNotNull($entreprise->getId());

        $this->em->clear();

        $trouve = $this->em->find(Entreprise::class, $entreprise->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('TechCorp SARL', $trouve->getNom());
        $this->assertSame('12345678901234', $trouve->getSiret());
        $this->assertSame('Informatique', $trouve->getSecteur());
        $this->assertNull($trouve->getCompte());
    }
}
