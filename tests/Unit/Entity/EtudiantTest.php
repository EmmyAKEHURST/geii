<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Etudiant;

class EtudiantTest extends EntityValidationTestCase
{
    private function createEtudiantValide(): Etudiant
    {
        return (new Etudiant())
            ->setNumEtudiant('ETU2024001')
            ->setNom('Dupont')
            ->setPrenom('Alice')
            ->setAnnee(1)
        ;
    }

    public function testEtudiantValide(): void
    {
        $violations = $this->validator->validate($this->createEtudiantValide());
        $this->assertCount(0, $violations);
    }

    public function testNumeroEtudiantVide(): void
    {
        $etudiant = $this->createEtudiantValide();
        $etudiant->setNumEtudiant('');

        $violations = $this->validator->validate($etudiant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le numéro étudiant ne peut pas être vide.', $messages);
    }

    public function testNomVide(): void
    {
        $etudiant = $this->createEtudiantValide();
        $etudiant->setNom('');

        $violations = $this->validator->validate($etudiant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le nom ne peut pas être vide.', $messages);
    }

    public function testPrenomVide(): void
    {
        $etudiant = $this->createEtudiantValide();
        $etudiant->setPrenom('');

        $violations = $this->validator->validate($etudiant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le prénom ne peut pas être vide.', $messages);
    }

    public function testAnneeNonRenseignee(): void
    {
        // annee est null par défaut, on ne l'initialise pas
        $etudiant = new Etudiant();
        $etudiant->setNumEtudiant('ETU2024002');
        $etudiant->setNom('Durand');
        $etudiant->setPrenom('Bob');

        $violations = $this->validator->validate($etudiant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'année doit être spécifiée.", $messages);
    }

    public function testAnneeZero(): void
    {
        $etudiant = $this->createEtudiantValide();
        $etudiant->setAnnee(0);

        $violations = $this->validator->validate($etudiant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'année doit être un entier positif.", $messages);
    }

    public function testAnneeNegative(): void
    {
        $etudiant = $this->createEtudiantValide();
        $etudiant->setAnnee(-1);

        $violations = $this->validator->validate($etudiant);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'année doit être un entier positif.", $messages);
    }
}
