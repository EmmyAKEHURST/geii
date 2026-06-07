<?php

namespace App\Tests\Unit\Entity;

use DateTime;
use App\Entity\SupportCours;

class SupportCoursTest extends EntityValidationTestCase
{
    private function createSupportCoursValide(): SupportCours
    {
        return (new SupportCours())
            ->setTitre('Cours d\'électronique numérique')
            ->setFichierPath('uploads/cours/electronique_numerique.pdf')
            ->setDateDepot(new DateTime('2024-09-01'))
        ;
    }

    public function testSupportCoursValide(): void
    {
        $violations = $this->validator->validate($this->createSupportCoursValide());
        $this->assertCount(0, $violations);
    }

    public function testTitreVide(): void
    {
        $support = $this->createSupportCoursValide();
        $support->setTitre('');

        $violations = $this->validator->validate($support);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le titre ne peut pas être vide.', $messages);
    }

    public function testTitreTropLong(): void
    {
        $support = $this->createSupportCoursValide();
        $support->setTitre(str_repeat('a', 256));

        $violations = $this->validator->validate($support);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn ($m) => str_contains($m, '255'));
        $this->assertNotEmpty($found);
    }

    public function testDateDepotNonRenseignee(): void
    {
        $support = new SupportCours();
        $support->setTitre('Cours d\'électronique');
        $support->setFichierPath('uploads/cours/fichier.pdf');

        $violations = $this->validator->validate($support);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La date de dépôt doit être spécifiée.', $messages);
    }
}
