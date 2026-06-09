<?php

namespace App\Tests\Repository;

use App\Entity\SupportCours;
use App\Repository\SupportCoursRepository;
use App\Tests\IntegrationTestCase;
use DateMalformedStringException;
use DateTime;

class SupportCoursRepositoryTest extends IntegrationTestCase
{
    private SupportCoursRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->em->getRepository(SupportCours::class);
    }

    /**
     * @throws DateMalformedStringException
     */
    private function createSupportCours(string $titre, string $date = '2024-09-01'): SupportCours
    {
        $support = (new SupportCours())
            ->setTitre($titre)
            ->setFichierPath('uploads/' . strtolower(str_replace(' ', '_', $titre)) . '.pdf')
            ->setDateDepot(new DateTime($date))
        ;

        $this->em->persist($support);

        return $support;
    }

    /**
     * Vérifie que find retourne null pour un identifiant inexistant.
     */
    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    /**
     * Vérifie qu'un support de cours est retrouvable par son identifiant.
     *
     * @throws DateMalformedStringException
     */
    public function testFindParId(): void
    {
        $support = $this->createSupportCours('Introduction aux microcontrôleurs');
        $this->em->flush();

        $trouve = $this->repository->find($support->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Introduction aux microcontrôleurs', $trouve->getTitre());
    }

    /**
     * Vérifie que findAll retourne tous les supports de cours persistés.
     *
     * @throws DateMalformedStringException
     */
    public function testFindAll(): void
    {
        $this->createSupportCours('Cours 1');
        $this->createSupportCours('Cours 2');
        $this->createSupportCours('Cours 3');
        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    /**
     * Vérifie que findOneBy retrouve un support par son titre ou retourne null si absent.
     *
     * @throws DateMalformedStringException
     */
    public function testFindOneByTitre(): void
    {
        $this->createSupportCours('Électronique numérique');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['titre' => 'Électronique numérique']);
        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['titre' => 'Inexistant']));
    }
}
