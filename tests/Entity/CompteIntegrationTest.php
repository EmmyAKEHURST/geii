<?php

namespace App\Tests\Entity;

use App\Entity\Compte;
use App\Tests\IntegrationTestCase;

class CompteIntegrationTest extends IntegrationTestCase
{
    private function createCompteValide(string $email = 'test@exemple.com'): Compte
    {
        return (new Compte())
            ->setEmail($email)
            ->setPassword('$2y$13$hashValide')
        ;
    }

    /**
     * Vérifie qu'un compte est correctement persisté en base et récupérable avec son email et son rôle ROLE_USER.
     */
    public function testPersistanceCompte(): void
    {
        $compte = $this->createCompteValide();

        $this->em->persist($compte);
        $this->em->flush();

        $this->assertNotNull($compte->getId());

        $this->em->clear();

        $trouve = $this->em->find(Compte::class, $compte->getId());

        $this->assertNotNull($trouve);
        $this->assertSame('test@exemple.com', $trouve->getEmail());
        $this->assertFalse($trouve->isVerified());
        $this->assertContains('ROLE_USER', $trouve->getRoles());
    }

    /**
     * Vérifie qu'un second compte avec le même email déclenche une violation de contrainte UniqueEntity.
     */
    public function testEmailUnique(): void
    {
        $compte1 = $this->createCompteValide('doublon@exemple.com');
        $this->em->persist($compte1);
        $this->em->flush();

        // Un deuxième compte avec le même email doit déclencher une violation UniqueEntity
        $compte2 = $this->createCompteValide('doublon@exemple.com');
        $violations = $this->validator->validate($compte2);

        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Cette adresse email existe déjà', $messages);
    }
}
