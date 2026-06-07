<?php

namespace App\Tests\Repository;

use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use App\Tests\IntegrationTestCase;

class EntrepriseRepositoryTest extends IntegrationTestCase
{
    private EntrepriseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->em->getRepository(Entreprise::class);
    }

    private function createEntreprise(string $nom, string $siret, string $secteur = 'Informatique'): Entreprise
    {
        $entreprise = new Entreprise();
        $entreprise->setNom($nom);
        $entreprise->setSiret($siret);
        $entreprise->setAdresse('1 rue Test, 75001 Paris');
        $entreprise->setSecteur($secteur);

        $this->em->persist($entreprise);

        return $entreprise;
    }

    /**
     * Vérifie que find retourne null pour un identifiant inexistant.
     */
    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    /**
     * Vérifie qu'une entreprise est retrouvable par son identifiant avec ses données.
     */
    public function testFindParId(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->em->flush();

        $trouve = $this->repository->find($entreprise->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('TechCorp', $trouve->getNom());
        $this->assertSame('12345678901234', $trouve->getSiret());
    }

    /**
     * Vérifie que findAll retourne l'ensemble des entreprises persistées.
     */
    public function testFindAll(): void
    {
        $this->createEntreprise('Alpha', '11111111111111');
        $this->createEntreprise('Beta', '22222222222222');
        $this->createEntreprise('Gamma', '33333333333333');
        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    /**
     * Vérifie que findBy filtre correctement les entreprises par secteur d'activité.
     */
    public function testFindBySecteur(): void
    {
        $this->createEntreprise('Alpha', '11111111111111', 'Industrie');
        $this->createEntreprise('Beta', '22222222222222', 'Industrie');
        $this->createEntreprise('Gamma', '33333333333333', 'Services');

        $this->em->flush();

        $industrie = $this->repository->findBy(['secteur' => 'Industrie']);
        $this->assertCount(2, $industrie);

        $services = $this->repository->findBy(['secteur' => 'Services']);
        $this->assertCount(1, $services);
    }

    /**
     * Vérifie que findOneBy retrouve une entreprise par son nom ou retourne null si absente.
     */
    public function testFindOneByNom(): void
    {
        $this->createEntreprise('TechCorp', '12345678901234');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['nom' => 'TechCorp']);
        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['nom' => 'Inexistant']));
    }
}
