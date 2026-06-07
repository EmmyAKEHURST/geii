<?php

namespace App\Tests\Unit\Entity;

use DateTime;
use App\Entity\EmploiDuTemps;

class EmploiDuTempsTest extends EntityValidationTestCase
{
    private function createEmploiDuTempsValide(): EmploiDuTemps
    {
        return (new EmploiDuTemps())
            ->setDateHeureDebut(new DateTime('2024-09-02 08:00:00'))
            ->setDateHeureFin(new DateTime('2024-09-02 10:00:00'))
            ->setSalle('A101')
        ;
    }

    public function testEmploiDuTempsValide(): void
    {
        $violations = $this->validator->validate($this->createEmploiDuTempsValide());
        $this->assertCount(0, $violations);
    }

    public function testDateDebutNonRenseignee(): void
    {
        $emploi = new EmploiDuTemps();
        $emploi->setDateHeureFin(new DateTime('2024-09-02 10:00:00'));
        $emploi->setSalle('A101');

        $violations = $this->validator->validate($emploi);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La date et heure de début doit être spécifiée.', $messages);
    }

    public function testDateFinNonRenseignee(): void
    {
        $emploi = new EmploiDuTemps();
        $emploi->setDateHeureDebut(new DateTime('2024-09-02 08:00:00'));
        $emploi->setSalle('A101');

        $violations = $this->validator->validate($emploi);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La date et heure de fin doit être spécifiée.', $messages);
    }

    public function testDateFinAvantDateDebut(): void
    {
        $emploi = $this->createEmploiDuTempsValide();
        $emploi->setDateHeureFin(new DateTime('2024-09-02 07:00:00'));

        $violations = $this->validator->validate($emploi);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La date de fin doit être postérieure à la date de début.', $messages);
    }

    public function testDateFinEgaleADateDebut(): void
    {
        $emploi = $this->createEmploiDuTempsValide();
        // GreaterThan (strictement supérieur), donc égal est invalide
        $emploi->setDateHeureFin(new DateTime('2024-09-02 08:00:00'));

        $violations = $this->validator->validate($emploi);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La date de fin doit être postérieure à la date de début.', $messages);
    }

    public function testSalleVide(): void
    {
        $emploi = $this->createEmploiDuTempsValide();
        $emploi->setSalle('');

        $violations = $this->validator->validate($emploi);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La salle ne peut pas être vide.', $messages);
    }
}
