<?php

namespace App\Tests\Repository;

use App\Entity\Entreprise;
use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;
use App\Repository\OffreAlternanceRepository;
use App\Tests\IntegrationTestCase;

class OffreAlternanceRepositoryTest extends IntegrationTestCase
{
    private OffreAlternanceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(OffreAlternance::class);
    }

    private function createEntreprise(string $nom, string $siret): Entreprise
    {
        $entreprise = new Entreprise();
        $entreprise->setNom($nom);
        $entreprise->setSiret($siret);
        $entreprise->setAdresse('1 rue Test, 75001 Paris');
        $entreprise->setSecteur('Informatique');

        $this->em->persist($entreprise);

        return $entreprise;
    }

    private function createOffre(string $titre, Entreprise $entreprise): OffreAlternance
    {
        $offre = new OffreAlternance();
        $offre->setTitre($titre);
        $offre->setDescription('Description de l\'offre.');
        $offre->setDuree(12);
        $offre->setStatut(StatutAlternance::ACTIVE);
        $offre->setEntreprise($entreprise);

        $this->em->persist($offre);

        return $offre;
    }

    // ── findByEntrepriseOrdered ──────────────────────────────────────────────

    /**
     * Vérifie que findByEntrepriseOrdered retourne un tableau vide sans offres.
     */
    public function testFindByEntrepriseOrderedRetourneTableauVideSiAucuneOffre(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->em->flush();

        $this->assertSame([], $this->repository->findByEntrepriseOrdered($entreprise));
    }

    /**
     * Vérifie que les offres sont triées par identifiant décroissant.
     */
    public function testFindByEntrepriseOrderedRetourneLesOffresTrieesParIdDesc(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $offre1 = $this->createOffre('Offre A', $entreprise);
        $offre2 = $this->createOffre('Offre B', $entreprise);
        $offre3 = $this->createOffre('Offre C', $entreprise);

        $this->em->flush();

        $result = $this->repository->findByEntrepriseOrdered($entreprise);

        $this->assertCount(3, $result);

        // Ordre DESC par ID : offre3, offre2, offre1
        $this->assertSame($offre3->getId(), $result[0]->getId());
        $this->assertSame($offre2->getId(), $result[1]->getId());
        $this->assertSame($offre1->getId(), $result[2]->getId());
    }

    /**
     * Vérifie que seules les offres de l'entreprise ciblée sont retournées.
     */
    public function testFindByEntrepriseOrderedExclutLesOffresDesAutresEntreprises(): void
    {
        $entreprise1 = $this->createEntreprise('Société Alpha', '11111111111111');
        $entreprise2 = $this->createEntreprise('Société Beta', '22222222222222');

        $this->createOffre('Offre Alpha', $entreprise1);
        $this->createOffre('Offre Beta', $entreprise2);
        $this->em->flush();

        $result = $this->repository->findByEntrepriseOrdered($entreprise1);

        $this->assertCount(1, $result);
        $this->assertSame('Offre Alpha', $result[0]->getTitre());
    }

    // ── countByEntreprise ────────────────────────────────────────────────────

    /**
     * Vérifie que countByEntreprise retourne 0 sans offres.
     */
    public function testCountByEntrepriseRetourneZeroSiAucuneOffre(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->em->flush();

        $this->assertSame(0, $this->repository->countByEntreprise($entreprise));
    }

    /**
     * Vérifie que countByEntreprise compte correctement les offres de l'entreprise.
     */
    public function testCountByEntrepriseCompteCorrectement(): void
    {
        $entreprise = $this->createEntreprise('TechCorp', '12345678901234');
        $this->createOffre('Offre 1', $entreprise);
        $this->createOffre('Offre 2', $entreprise);
        $this->createOffre('Offre 3', $entreprise);
        $this->em->flush();

        $this->assertSame(3, $this->repository->countByEntreprise($entreprise));
    }

    /**
     * Vérifie que countByEntreprise ne comptabilise pas les offres des autres entreprises.
     */
    public function testCountByEntrepriseExclutLesOffresDesAutresEntreprises(): void
    {
        $entreprise1 = $this->createEntreprise('Société Alpha', '11111111111111');
        $entreprise2 = $this->createEntreprise('Société Beta', '22222222222222');

        $this->createOffre('Offre Alpha 1', $entreprise1);
        $this->createOffre('Offre Alpha 2', $entreprise1);
        $this->createOffre('Offre Beta 1', $entreprise2);
        $this->em->flush();

        $this->assertSame(2, $this->repository->countByEntreprise($entreprise1));
        $this->assertSame(1, $this->repository->countByEntreprise($entreprise2));
    }
}
