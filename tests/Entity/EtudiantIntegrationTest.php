<?php

namespace App\Tests\Entity;

use App\Entity\Etudiant;
use App\Tests\IntegrationTestCase;

class EtudiantIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'un étudiant est correctement persisté et récupérable avec ses données.
     */
    public function testPersistanceEtudiant(): void
    {
        $etudiant = (new Etudiant())
            ->setNumEtudiant('ETU2024001')
            ->setNom('Dupont')
            ->setPrenom('Alice')
            ->setAnnee(1)
        ;

        $this->em->persist($etudiant);
        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(Etudiant::class, 'ETU2024001');

        $this->assertNotNull($trouve);
        $this->assertSame('Dupont', $trouve->getNom());
        $this->assertSame('Alice', $trouve->getPrenom());
        $this->assertSame(1, $trouve->getAnnee());
    }

    /**
     * Vérifie que plusieurs étudiants peuvent être persistés et retrouvés via findAll.
     */
    public function testPersistancePlusieursEtudiants(): void
    {
        $etudiants = [
            'ETU001' => 'Dupont',
            'ETU002' => 'Durand',
            'ETU003' => 'Martin'
        ];

        foreach ($etudiants as $num => $nom) {
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
