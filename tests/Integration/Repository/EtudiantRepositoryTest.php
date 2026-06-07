<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use App\Tests\Integration\IntegrationTestCase;

class EtudiantRepositoryTest extends IntegrationTestCase
{
    private EtudiantRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(Etudiant::class);
    }

    private function createEtudiant(string $num, string $nom, int $annee = 1): Etudiant
    {
        $etudiant = new Etudiant();
        $etudiant->setNumEtudiant($num);
        $etudiant->setNom($nom);
        $etudiant->setPrenom('Prénom');
        $etudiant->setAnnee($annee);
        $this->em->persist($etudiant);
        return $etudiant;
    }

    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find('INEXISTANT'));
    }

    public function testFindParNumeroEtudiant(): void
    {
        $this->createEtudiant('ETU001', 'Dupont');
        $this->em->flush();

        $trouve = $this->repository->find('ETU001');
        $this->assertNotNull($trouve);
        $this->assertSame('Dupont', $trouve->getNom());
    }

    public function testFindAll(): void
    {
        $this->createEtudiant('ETU001', 'Dupont', 1);
        $this->createEtudiant('ETU002', 'Durand', 2);
        $this->createEtudiant('ETU003', 'Martin', 1);
        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    public function testFindByAnnee(): void
    {
        $this->createEtudiant('ETU001', 'Dupont', 1);
        $this->createEtudiant('ETU002', 'Durand', 2);
        $this->createEtudiant('ETU003', 'Martin', 1);
        $this->em->flush();

        $annee1 = $this->repository->findBy(['annee' => 1]);
        $this->assertCount(2, $annee1);

        $annee2 = $this->repository->findBy(['annee' => 2]);
        $this->assertCount(1, $annee2);
    }

    public function testFindOneByNom(): void
    {
        $this->createEtudiant('ETU001', 'Dupont');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['nom' => 'Dupont']);
        $this->assertNotNull($trouve);
        $this->assertSame('ETU001', $trouve->getNumEtudiant());

        $this->assertNull($this->repository->findOneBy(['nom' => 'Inexistant']));
    }
}
