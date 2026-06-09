<?php

namespace App\Tests\Repository;

use App\Entity\Compte;
use App\Repository\CompteRepository;
use App\Tests\IntegrationTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompteRepositoryTest extends IntegrationTestCase
{
    private const string MOT_DE_PASSE = 'MotDePasse123!';

    private CompteRepository $repository;
    private UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->em->getRepository(Compte::class);
        $this->hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    private function createCompte(string $email): Compte
    {
        $compte = (new Compte())->setEmail($email);
        $compte->setPassword($this->hasher->hashPassword($compte, self::MOT_DE_PASSE));

        $this->em->persist($compte);

        return $compte;
    }

    /**
     * Vérifie qu'upgradePassword() met bien à jour le hash en base.
     */
    public function testUpgradePasswordMetsAJourLeMotDePasse(): void
    {
        $compte = $this->createCompte('test@exemple.com');
        $this->em->flush();

        $this->repository->upgradePassword($compte, '$2y$13$nouveauHash');

        $this->em->clear();
        $trouve = $this->em->find(Compte::class, $compte->getId());
        $this->assertSame('$2y$13$nouveauHash', $trouve->getPassword());
    }

    /**
     * Vérifie que find retourne null pour un identifiant inexistant.
     */
    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    /**
     * Vérifie que findAll retourne tous les comptes persistés.
     */
    public function testFindAll(): void
    {
        $this->createCompte('alice@exemple.com');
        $this->createCompte('bob@exemple.com');

        $this->em->flush();

        $this->assertCount(2, $this->repository->findAll());
    }

    /**
     * Vérifie que findOneBy permet de retrouver un compte par son email.
     */
    public function testFindOneByEmail(): void
    {
        $this->createCompte('charlie@exemple.com');

        $this->em->flush();

        $trouve = $this->repository->findOneBy(['email' => 'charlie@exemple.com']);

        $this->assertNotNull($trouve);
        $this->assertSame('charlie@exemple.com', $trouve->getEmail());
    }

    /**
     * Vérifie que findOneBy retourne null pour un email absent.
     */
    public function testFindOneByEmailRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->findOneBy(['email' => 'inexistant@exemple.com']));
    }
}
