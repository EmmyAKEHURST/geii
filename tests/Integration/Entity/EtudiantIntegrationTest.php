<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Etudiant;
use App\Tests\Integration\IntegrationTestCase;

class EtudiantIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceEtudiant(): void
    {
        $etudiant = new Etudiant();
        $etudiant->setNumEtudiant('ETU2024001');
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Alice');
        $etudiant->setAnnee(1);

        $this->em->persist($etudiant);
        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(Etudiant::class, 'ETU2024001');
        $this->assertNotNull($trouve);
        $this->assertSame('Dupont', $trouve->getNom());
        $this->assertSame('Alice', $trouve->getPrenom());
        $this->assertSame(1, $trouve->getAnnee());
    }

    public function testPersistancePlusieursEtudiants(): void
    {
        foreach (['ETU001' => 'Dupont', 'ETU002' => 'Durand', 'ETU003' => 'Martin'] as $num => $nom) {
            $etudiant = new Etudiant();
            $etudiant->setNumEtudiant($num);
            $etudiant->setNom($nom);
            $etudiant->setPrenom('Prénom');
            $etudiant->setAnnee(2);
            $this->em->persist($etudiant);
        }
        $this->em->flush();

        $this->em->clear();

        $repository = $this->em->getRepository(Etudiant::class);
        $this->assertCount(3, $repository->findAll());
    }
}
