<?php

namespace App\Tests\Unit\Entity;

use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;

class OffreAlternanceTest extends EntityValidationTestCase
{
    private function createOffreValide(): OffreAlternance
    {
        return (new OffreAlternance())
            ->setTitre('Développeur PHP H/F')
            ->setDescription('Rejoignez notre équipe pour développer des applications web modernes.')
            ->setDuree(12)
            ->setStatut(StatutAlternance::ACTIVE)
        ;
    }

    public function testOffreValide(): void
    {
        $violations = $this->validator->validate($this->createOffreValide());
        $this->assertCount(0, $violations);
    }

    public function testTitreVide(): void
    {
        $offre = $this->createOffreValide();
        $offre->setTitre('');

        $violations = $this->validator->validate($offre);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains("Le titre de l'offre ne peut pas être vide.", $messages);
    }

    public function testDescriptionVide(): void
    {
        $offre = $this->createOffreValide();
        $offre->setDescription('');

        $violations = $this->validator->validate($offre);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La description ne peut pas être vide.', $messages);
    }

    public function testDureeNonRenseignee(): void
    {
        // duree est null par défaut
        $offre = new OffreAlternance();
        $offre->setTitre('Stage PHP');
        $offre->setDescription('Description.');
        $offre->setStatut(StatutAlternance::ACTIVE);

        $violations = $this->validator->validate($offre);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La durée doit être spécifiée.', $messages);
    }

    public function testDureeZero(): void
    {
        $offre = $this->createOffreValide();
        $offre->setDuree(0);

        $violations = $this->validator->validate($offre);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La durée doit être un entier positif (en mois).', $messages);
    }

    public function testDureeNegative(): void
    {
        $offre = $this->createOffreValide();
        $offre->setDuree(-6);

        $violations = $this->validator->validate($offre);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La durée doit être un entier positif (en mois).', $messages);
    }

    public function testStatutNonRenseigne(): void
    {
        // statut est null par défaut
        $offre = new OffreAlternance();
        $offre->setTitre('Stage PHP');
        $offre->setDescription('Description.');
        $offre->setDuree(6);

        $violations = $this->validator->validate($offre);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le statut doit être spécifié.', $messages);
    }
}
