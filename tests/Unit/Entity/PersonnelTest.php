<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Personnel;

class PersonnelTest extends EntityValidationTestCase
{
    private function createPersonnelValide(): Personnel
    {
        return (new Personnel())
            ->setNom('Leclerc')
            ->setPrenom('Marie')
            ->setFonction('Secrétaire pédagogique')
            ->setAdmin(false)
        ;
    }

    public function testPersonnelValide(): void
    {
        $violations = $this->validator->validate($this->createPersonnelValide());
        $this->assertCount(0, $violations);
    }

    public function testPersonnelAdminValide(): void
    {
        $personnel = $this->createPersonnelValide();
        $personnel->setAdmin(true);

        $violations = $this->validator->validate($personnel);
        $this->assertCount(0, $violations);
    }

    public function testNomVide(): void
    {
        $personnel = $this->createPersonnelValide();
        $personnel->setNom('');

        $violations = $this->validator->validate($personnel);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le nom ne peut pas être vide.', $messages);
    }

    public function testPrenomVide(): void
    {
        $personnel = $this->createPersonnelValide();
        $personnel->setPrenom('');

        $violations = $this->validator->validate($personnel);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le prénom ne peut pas être vide.', $messages);
    }

    public function testFonctionVide(): void
    {
        $personnel = $this->createPersonnelValide();
        $personnel->setFonction('');

        $violations = $this->validator->validate($personnel);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La fonction ne peut pas être vide.', $messages);
    }

    public function testAdminNonRenseigne(): void
    {
        // admin est null par défaut
        $personnel = new Personnel();
        $personnel->setNom('Leclerc');
        $personnel->setPrenom('Marie');
        $personnel->setFonction('Secrétaire');

        $violations = $this->validator->validate($personnel);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le statut administrateur doit être renseigné.', $messages);
    }
}
