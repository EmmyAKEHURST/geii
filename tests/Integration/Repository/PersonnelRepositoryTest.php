<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Personnel;
use App\Repository\PersonnelRepository;
use App\Tests\Integration\IntegrationTestCase;

class PersonnelRepositoryTest extends IntegrationTestCase
{
    private PersonnelRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(Personnel::class);
    }

    private function createPersonnel(string $nom, bool $admin = false): Personnel
    {
        $personnel = new Personnel();
        $personnel->setNom($nom);
        $personnel->setPrenom('Prénom');
        $personnel->setFonction('Agent administratif');
        $personnel->setAdmin($admin);
        $this->em->persist($personnel);
        return $personnel;
    }

    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    public function testFindParId(): void
    {
        $personnel = $this->createPersonnel('Leclerc', true);
        $this->em->flush();

        $trouve = $this->repository->find($personnel->getId());
        $this->assertNotNull($trouve);
        $this->assertSame('Leclerc', $trouve->getNom());
        $this->assertTrue($trouve->isAdmin());
    }

    public function testFindAll(): void
    {
        $this->createPersonnel('Leclerc');
        $this->createPersonnel('Morel');
        $this->em->flush();

        $this->assertCount(2, $this->repository->findAll());
    }

    public function testFindByAdmin(): void
    {
        $this->createPersonnel('Admin1', true);
        $this->createPersonnel('Admin2', true);
        $this->createPersonnel('Standard', false);
        $this->em->flush();

        $admins = $this->repository->findBy(['admin' => true]);
        $this->assertCount(2, $admins);

        $nonAdmins = $this->repository->findBy(['admin' => false]);
        $this->assertCount(1, $nonAdmins);
    }

    public function testFindOneByNom(): void
    {
        $this->createPersonnel('Bertrand');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['nom' => 'Bertrand']);
        $this->assertNotNull($trouve);
        $this->assertNull($this->repository->findOneBy(['nom' => 'Inexistant']));
    }
}
