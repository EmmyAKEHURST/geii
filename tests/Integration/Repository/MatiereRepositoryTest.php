<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Matiere;
use App\Repository\MatiereRepository;
use App\Tests\Integration\IntegrationTestCase;

class MatiereRepositoryTest extends IntegrationTestCase
{
    private MatiereRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(Matiere::class);
    }

    private function createMatiere(string $nom): Matiere
    {
        $matiere = new Matiere();
        $matiere->setNom($nom);
        $this->em->persist($matiere);
        return $matiere;
    }

    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    public function testFindParId(): void
    {
        $matiere = $this->createMatiere('Mathématiques');
        $this->em->flush();

        $trouve = $this->repository->find($matiere->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Mathématiques', $trouve->getNom());
    }

    public function testFindAll(): void
    {
        $this->createMatiere('Mathématiques');
        $this->createMatiere('Physique');
        $this->createMatiere('Informatique');
        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    public function testFindOneByNom(): void
    {
        $this->createMatiere('Électronique');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['nom' => 'Électronique']);
        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['nom' => 'Inexistant']));
    }
}
