<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Compte;
use App\Repository\CompteRepository;
use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CompteRepositoryTest extends IntegrationTestCase
{
    private CompteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->em->getRepository(Compte::class);
    }

    private function createCompte(string $email): Compte
    {
        $compte = new Compte();
        $compte->setEmail($email);
        $compte->setPassword('$2y$13$hashInitial');
        $this->em->persist($compte);
        return $compte;
    }

    public function testUpgradePasswordMetsAJourLeMotDePasse(): void
    {
        $compte = $this->createCompte('test@exemple.com');
        $this->em->flush();

        $this->repository->upgradePassword($compte, '$2y$13$nouveauHash');

        $this->em->clear();
        $trouve = $this->em->find(Compte::class, $compte->getId());
        $this->assertSame('$2y$13$nouveauHash', $trouve->getPassword());
    }

    public function testUpgradePasswordRejeteUnNonCompte(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $fakeUser = new class implements UserInterface, PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string { return 'hash'; }
            public function getUserIdentifier(): string { return 'fake'; }
            public function getRoles(): array { return []; }
            public function eraseCredentials(): void {}
        };

        $this->repository->upgradePassword($fakeUser, 'nouveau_hash');
    }

    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    public function testFindAll(): void
    {
        $this->createCompte('alice@exemple.com');
        $this->createCompte('bob@exemple.com');
        $this->em->flush();

        $this->assertCount(2, $this->repository->findAll());
    }

    public function testFindOneByEmail(): void
    {
        $this->createCompte('charlie@exemple.com');
        $this->em->flush();

        $trouve = $this->repository->findOneBy(['email' => 'charlie@exemple.com']);
        $this->assertNotNull($trouve);
        $this->assertSame('charlie@exemple.com', $trouve->getEmail());
    }

    public function testFindOneByEmailRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->findOneBy(['email' => 'inexistant@exemple.com']));
    }
}
