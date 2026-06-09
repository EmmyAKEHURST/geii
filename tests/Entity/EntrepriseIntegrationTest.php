<?php

namespace App\Tests\Entity;

use App\Entity\Entreprise;
use App\Tests\IntegrationTestCase;

class EntrepriseIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'une entreprise est persistée avec ses champs et sans compte lié.
     */
    public function testPersistanceEntreprise(): void
    {
        $entreprise = (new Entreprise())
            ->setNom('TechCorp SARL')
            ->setSiret('12345678901234')
            ->setAdresse('12 rue de la Paix, 75001 Paris')
            ->setSecteur('Informatique')
        ;

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
