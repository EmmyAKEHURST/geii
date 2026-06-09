<?php

namespace App\Tests\Repository;

use App\Entity\Enseignant;
use App\Repository\EnseignantRepository;
use App\Tests\IntegrationTestCase;

class EnseignantRepositoryTest extends IntegrationTestCase
{
    private EnseignantRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(Enseignant::class);
    }

    private function createEnseignant(string $nom, string $specialite = 'Informatique'): Enseignant
    {
        $enseignant = new Enseignant();
        $enseignant->setNom($nom);
        $enseignant->setPrenom('Prénom');
        $enseignant->setSpecialite($specialite);
        $enseignant->setBureau('A100');
        $this->em->persist($enseignant);
        return $enseignant;
    }

    /**
     * Vérifie que find retourne null pour un identifiant inexistant.
     */
    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    /**
     * Vérifie qu'un enseignant est retrouvable par son identifiant.
     */
    public function testFindParId(): void
    {
        $enseignant = $this->createEnseignant('Martin');

        $this->em->flush();

        $trouve = $this->repository->find($enseignant->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Martin', $trouve->getNom());
    }

    /**
     * Vérifie que findAll retourne tous les enseignants persistés.
     */
    public function testFindAll(): void
    {
        $this->createEnseignant('Martin');
        $this->createEnseignant('Bernard');

        $this->em->flush();

        $this->assertCount(2, $this->repository->findAll());
    }

    /**
     * Vérifie que findBy filtre correctement les enseignants par spécialité.
     */
    public function testFindBySpecialite(): void
    {
        $this->createEnseignant('Martin', 'Électronique');
        $this->createEnseignant('Bernard', 'Électronique');
        $this->createEnseignant('Dupuis');

        $this->em->flush();

        $electronique = $this->repository->findBy(['specialite' => 'Électronique']);
        $this->assertCount(2, $electronique);
    }

    /**
     * Vérifie que findOneBy retrouve un enseignant par son nom ou retourne null si absent.
     */
    public function testFindOneByNom(): void
    {
        $this->createEnseignant('Leroy');

        $this->em->flush();

        $trouve = $this->repository->findOneBy(['nom' => 'Leroy']);
        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['nom' => 'Inexistant']));
    }
}
