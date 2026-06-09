<?php

namespace App\Tests;

use App\Entity\Compte;
use App\Entity\Enseignant;
use App\Entity\Entreprise;
use App\Entity\Etudiant;
use App\Entity\Personnel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');

        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        $this->em->close();
        parent::tearDown();
    }

    protected function createCompte(string $email, array $roles = [], string $plainPassword = 'Password1!'): Compte
    {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $compte = (new Compte())
            ->setEmail($email)
            ->setRoles($roles)
            ->setIsVerified(true)
        ;

        $compte->setPassword($hasher->hashPassword($compte, $plainPassword));

        $this->em->persist($compte);
        $this->em->flush();

        return $compte;
    }

    protected function createCompteEtudiant(string $email): Compte
    {
        $compte = $this->createCompte($email, ['ROLE_ETUDIANT']);

        $etudiant = (new Etudiant())
            ->setNumEtudiant('E' . rand(10000, 99999))
            ->setNom('TestNom')
            ->setPrenom('TestPrenom')
            ->setAnnee(1)
            ->setCompte($compte)
        ;

        $this->em->persist($etudiant);
        $this->em->flush();

        // Rafraîchit le compte pour que Doctrine charge le côté inverse de la relation
        $this->em->refresh($compte);

        return $compte;
    }

    protected function createCompteEnseignant(string $email): Compte
    {
        $compte = $this->createCompte($email, ['ROLE_ENSEIGNANT']);

        $enseignant = (new Enseignant())
            ->setNom('TestNom')
            ->setPrenom('TestPrenom')
            ->setSpecialite('Informatique')
            ->setBureau('A101')
            ->setCompte($compte)
        ;

        $this->em->persist($enseignant);
        $this->em->flush();

        return $compte;
    }

    protected function createCompteEntreprise(string $email): Compte
    {
        $compte = $this->createCompte($email, ['ROLE_ENTREPRISE']);

        $entreprise = (new Entreprise())
            ->setNom('TestEntreprise')
            ->setSiret('12345678901234')
            ->setAdresse('1 rue de la Paix')
            ->setSecteur('Informatique')
        ;

        $compte->setEntreprise($entreprise);

        $this->em->persist($entreprise);
        $this->em->flush();

        return $compte;
    }

    protected function createComptePersonnel(string $email): Compte
    {
        $compte = $this->createCompte($email, ['ROLE_PERSONNEL']);

        $personnel = (new Personnel())
            ->setNom('TestNom')
            ->setPrenom('TestPrenom')
            ->setFonction('Administration')
            ->setAdmin(false)
            ->setCompte($compte)
        ;

        $this->em->persist($personnel);
        $this->em->flush();

        return $compte;
    }
}
