<?php

namespace App\Tests\Integration\Repository;

use App\Entity\SupportCours;
use App\Repository\SupportCoursRepository;
use App\Tests\Integration\IntegrationTestCase;

class SupportCoursRepositoryTest extends IntegrationTestCase
{
    private SupportCoursRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(SupportCours::class);
    }

    private function createSupportCours(string $titre, string $date = '2024-09-01'): SupportCours
    {
        $support = new SupportCours();
        $support->setTitre($titre);
        $support->setFichierPath('uploads/' . strtolower(str_replace(' ', '_', $titre)) . '.pdf');
        $support->setDateDepot(new \DateTime($date));
        $this->em->persist($support);
        return $support;
    }

    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    public function testFindParId(): void
    {
        $support = $this->createSupportCours('Introduction aux microcontrôleurs');
        $this->em->flush();

        $trouve = $this->repository->find($support->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Introduction aux microcontrôleurs', $trouve->getTitre());
    }

    public function testFindAll(): void
    {
        $this->createSupportCours('Cours 1');
        $this->createSupportCours('Cours 2');
        $this->createSupportCours('Cours 3');
        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    public function testFindOneByTitre(): void
    {
        $this->createSupportCours('Électronique numérique');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['titre' => 'Électronique numérique']);
        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['titre' => 'Inexistant']));
    }
}
