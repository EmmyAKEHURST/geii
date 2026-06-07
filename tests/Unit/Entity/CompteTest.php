<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Compte;

class CompteTest extends EntityValidationTestCase
{
    private function createCompteValide(): Compte
    {
        return (new Compte())
            ->setEmail('utilisateur@exemple.com')
            ->setPassword('$2y$13$hashValide')
        ;
    }

    public function testCompteValide(): void
    {
        $violations = $this->validator->validate($this->createCompteValide());
        $this->assertCount(0, $violations);
    }

    public function testEmailVide(): void
    {
        $compte = $this->createCompteValide();
        $compte->setEmail('');
        $violations = $this->validator->validate($compte);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'adresse email ne peut pas être vide.", $messages);
    }

    public function testEmailFormatInvalide(): void
    {
        $compte = $this->createCompteValide();
        $compte->setEmail('pasunemail');
        $violations = $this->validator->validate($compte);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn($m) => str_contains($m, 'pasunemail'));
        $this->assertNotEmpty($found);
    }

    public function testEmailTropLong(): void
    {
        $compte = $this->createCompteValide();
        // local@domain.fr avec local de 170 'a' → total > 180
        $compte->setEmail(str_repeat('a', 170) . '@domaine.fr');
        $violations = $this->validator->validate($compte);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn($m) => str_contains($m, '180'));
        $this->assertNotEmpty($found);
    }
}
