<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Enseignant;

class EnseignantTest extends EntityValidationTestCase
{
    private function createEnseignantValide(): Enseignant
    {
        return (new Enseignant())
            ->setNom('Martin')
            ->setPrenom('Éric')
            ->setSpecialite('Électronique')
            ->setBureau('B204')
        ;
    }

    public function testEnseignantValide(): void
    {
        $violations = $this->validator->validate($this->createEnseignantValide());

        $this->assertCount(0, $violations);
    }

    public function testNomVide(): void
    {
        $enseignant = $this->createEnseignantValide();
        $enseignant->setNom('');
        $violations = $this->validator->validate($enseignant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le nom ne peut pas être vide.', $messages);
    }

    public function testNomTropLong(): void
    {
        $enseignant = $this->createEnseignantValide();
        $enseignant->setNom(str_repeat('a', 256));
        $violations = $this->validator->validate($enseignant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn ($m) => str_contains($m, '255'));
        $this->assertNotEmpty($found);
    }

    public function testPrenomVide(): void
    {
        $enseignant = $this->createEnseignantValide();
        $enseignant->setPrenom('');
        $violations = $this->validator->validate($enseignant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le prénom ne peut pas être vide.', $messages);
    }

    public function testSpecialiteVide(): void
    {
        $enseignant = $this->createEnseignantValide();
        $enseignant->setSpecialite('');
        $violations = $this->validator->validate($enseignant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La spécialité ne peut pas être vide.', $messages);
    }

    public function testBureauVide(): void
    {
        $enseignant = $this->createEnseignantValide();
        $enseignant->setBureau('');
        $violations = $this->validator->validate($enseignant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le bureau ne peut pas être vide.', $messages);
    }
}
