<?php

namespace App\Tests\Unit\Entity;

use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;

class ProjetTuteureTest extends EntityValidationTestCase
{
    private function createProjetValide(): ProjetTuteure
    {
        return (new ProjetTuteure())
            ->setTitre('Conception d\'une application IoT')
            ->setDescription('Développement d\'un système de supervision des équipements industriels.')
            ->setAnnee(2024)
            ->setStatut(StatutProjetTuteure::OUVERT)
        ;
    }

    public function testProjetValide(): void
    {
        $violations = $this->validator->validate($this->createProjetValide());
        $this->assertCount(0, $violations);
    }

    public function testTitreVide(): void
    {
        $projet = $this->createProjetValide();
        $projet->setTitre('');

        $violations = $this->validator->validate($projet);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le titre ne peut pas être vide.', $messages);
    }

    public function testDescriptionVide(): void
    {
        $projet = $this->createProjetValide();
        $projet->setDescription('');

        $violations = $this->validator->validate($projet);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La description ne peut pas être vide.', $messages);
    }

    public function testAnneeNonRenseignee(): void
    {
        $projet = new ProjetTuteure();
        $projet->setTitre('Projet IoT');
        $projet->setDescription('Description du projet.');
        $projet->setStatut(StatutProjetTuteure::OUVERT);

        $violations = $this->validator->validate($projet);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'année doit être spécifiée.", $messages);
    }

    public function testAnneeNegative(): void
    {
        $projet = $this->createProjetValide();
        $projet->setAnnee(-1);

        $violations = $this->validator->validate($projet);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("L'année doit être un entier positif.", $messages);
    }

    public function testStatutNonRenseigne(): void
    {
        $projet = new ProjetTuteure();
        $projet->setTitre('Projet IoT');
        $projet->setDescription('Description du projet.');
        $projet->setAnnee(2024);

        $violations = $this->validator->validate($projet);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le statut doit être spécifié.', $messages);
    }
}
