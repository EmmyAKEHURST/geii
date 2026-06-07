<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Matiere;

class MatiereTest extends EntityValidationTestCase
{
    private function createMatiereValide(): Matiere
    {
        return (new Matiere())
            ->setNom('Mathématiques')
        ;
    }

    public function testMatiereValide(): void
    {
        $violations = $this->validator->validate($this->createMatiereValide());
        $this->assertCount(0, $violations);
    }

    public function testNomVide(): void
    {
        $matiere = $this->createMatiereValide();
        $matiere->setNom('');

        $violations = $this->validator->validate($matiere);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le nom de la matière ne peut pas être vide.', $messages);
    }

    public function testNomTropLong(): void
    {
        $matiere = $this->createMatiereValide();
        $matiere->setNom(str_repeat('a', 256));

        $violations = $this->validator->validate($matiere);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn($m) => str_contains($m, '255'));
        $this->assertNotEmpty($found);
    }
}
