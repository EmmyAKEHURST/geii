<?php

namespace App\Tests\Repository;

use App\Entity\Matiere;
use App\Repository\MatiereRepository;
use App\Tests\IntegrationTestCase;

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
        $matiere = (new Matiere())->setNom($nom);

        $this->em->persist($matiere);

        return $matiere;
    }

    /**
     * Vérifie que find retourne null pour un identifiant inexistant.
     */
    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    /**
     * Vérifie qu'une matière est retrouvable par son identifiant.
     */
    public function testFindParId(): void
    {
        $matiere = $this->createMatiere('Mathématiques');
        $this->em->flush();

        $trouve = $this->repository->find($matiere->getId());

        $this->assertNotNull($trouve);
        $this->assertSame('Mathématiques', $trouve->getNom());
    }

    /**
     * Vérifie que findAll retourne toutes les matières persistées.
     */
    public function testFindAll(): void
    {
        $this->createMatiere('Mathématiques');
        $this->createMatiere('Physique');
        $this->createMatiere('Informatique');

        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    /**
     * Vérifie que findOneBy retrouve une matière par son nom ou retourne null si absente.
     */
    public function testFindOneByNom(): void
    {
        $this->createMatiere('Électronique');

        $this->em->flush();

        $trouve = $this->repository->findOneBy(['nom' => 'Électronique']);

        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['nom' => 'Inexistant']));
    }
}
