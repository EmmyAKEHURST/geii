<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Entreprise;

class EntrepriseTest extends EntityValidationTestCase
{
    private function createEntrepriseValide(): Entreprise
    {
        return (new Entreprise())
            ->setNom('TechCorp SARL')
            ->setSiret('12345678901234')
            ->setAdresse('12 rue de la Paix, 75001 Paris')
            ->setSecteur('Informatique')
        ;
    }

    public function testEntrepriseValide(): void
    {
        $violations = $this->validator->validate($this->createEntrepriseValide());
        $this->assertCount(0, $violations);
    }

    public function testNomVide(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setNom('');

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("Le nom de l'entreprise ne peut pas être vide.", $messages);
    }

    public function testSiretVide(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setSiret('');

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le numéro SIRET ne peut pas être vide.', $messages);
    }

    public function testSiretTropCourt(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setSiret('1234567890123'); // 13 chiffres au lieu de 14

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn($m) => str_contains($m, '14'));
        $this->assertNotEmpty($found);
    }

    public function testSiretTropLong(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setSiret('123456789012345'); // 15 chiffres

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
    }

    public function testSiretAvecLettres(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setSiret('1234567890123A'); // 14 chars, mais avec lettre

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le numéro SIRET doit seulement contenir des chiffres.', $messages);
    }

    public function testAdresseVide(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setAdresse('');

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'adresse ne peut pas être vide.", $messages);
    }

    public function testSecteurVide(): void
    {
        $entreprise = $this->createEntrepriseValide();
        $entreprise->setSecteur('');

        $violations = $this->validator->validate($entreprise);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("Le secteur d'activité ne peut pas être vide.", $messages);
    }
}
