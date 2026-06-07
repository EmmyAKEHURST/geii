<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Entreprise;
use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
use App\Repository\ProjetTuteureRepository;
use App\Tests\Integration\IntegrationTestCase;

class ProjetTuteureRepositoryTest extends IntegrationTestCase
{
    private ProjetTuteureRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(ProjetTuteure::class);
    }

    private function createEntreprise(string $nom, string $siret): Entreprise
    {
        $entreprise = new Entreprise();
        $entreprise->setNom($nom);
        $entreprise->setSiret($siret);
        $entreprise->setAdresse('1 rue Test, 75001 Paris');
        $entreprise->setSecteur('Industrie');
        $this->em->persist($entreprise);
        return $entreprise;
    }

    private function createProjet(string $titre, Entreprise $entreprise): ProjetTuteure
    {
        $projet = new ProjetTuteure();
        $projet->setTitre($titre);
        $projet->setDescription('Description du projet.');
        $projet->setAnnee(2024);
        $projet->setStatut(StatutProjetTuteure::OUVERT);
        $projet->setEntreprise($entreprise);
        $this->em->persist($projet);
        return $projet;
    }

    // ── findByEntrepriseOrdered ──────────────────────────────────────────────

    public function testFindByEntrepriseOrderedRetourneTableauVideSiAucunProjet(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->em->flush();

        $this->assertSame([], $this->repository->findByEntrepriseOrdered($entreprise));
    }

    public function testFindByEntrepriseOrderedRetourneLesProjetsTresesParIdDesc(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $projet1 = $this->createProjet('Projet A', $entreprise);
        $projet2 = $this->createProjet('Projet B', $entreprise);
        $projet3 = $this->createProjet('Projet C', $entreprise);
        $this->em->flush();

        $result = $this->repository->findByEntrepriseOrdered($entreprise);

        $this->assertCount(3, $result);
        $this->assertSame($projet3->getId(), $result[0]->getId());
        $this->assertSame($projet2->getId(), $result[1]->getId());
        $this->assertSame($projet1->getId(), $result[2]->getId());
    }

    public function testFindByEntrepriseOrderedExclutLesProjetsDesAutresEntreprises(): void
    {
        $entreprise1 = $this->createEntreprise('Société Alpha', '11111111111111');
        $entreprise2 = $this->createEntreprise('Société Beta', '22222222222222');

        $this->createProjet('Projet Alpha', $entreprise1);
        $this->createProjet('Projet Beta', $entreprise2);
        $this->em->flush();

        $result = $this->repository->findByEntrepriseOrdered($entreprise1);

        $this->assertCount(1, $result);
        $this->assertSame('Projet Alpha', $result[0]->getTitre());
    }

    // ── countByEntreprise ────────────────────────────────────────────────────

    public function testCountByEntrepriseRetourneZeroSiAucunProjet(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->em->flush();

        $this->assertSame(0, $this->repository->countByEntreprise($entreprise));
    }

    public function testCountByEntrepriseCompteCorrectement(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->createProjet('Projet 1', $entreprise);
        $this->createProjet('Projet 2', $entreprise);
        $this->em->flush();

        $this->assertSame(2, $this->repository->countByEntreprise($entreprise));
    }

    public function testCountByEntrepriseExclutLesProjetsDesAutresEntreprises(): void
    {
        $entreprise1 = $this->createEntreprise('Société Alpha', '11111111111111');
        $entreprise2 = $this->createEntreprise('Société Beta', '22222222222222');

        $this->createProjet('Projet Alpha 1', $entreprise1);
        $this->createProjet('Projet Alpha 2', $entreprise1);
        $this->createProjet('Projet Beta 1', $entreprise2);
        $this->em->flush();

        $this->assertSame(2, $this->repository->countByEntreprise($entreprise1));
        $this->assertSame(1, $this->repository->countByEntreprise($entreprise2));
    }
}
