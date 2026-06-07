<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Note;

class NoteTest extends EntityValidationTestCase
{
    private function createNoteValide(): Note
    {
        return (new Note())
            ->setValeur(12.5)
        ;
    }

    public function testNoteValide(): void
    {
        $violations = $this->validator->validate($this->createNoteValide());
        $this->assertCount(0, $violations);
    }

    public function testBorneMinimaleValide(): void
    {
        $note = $this->createNoteValide();
        $note->setValeur(0.0);

        $violations = $this->validator->validate($note);
        $this->assertCount(0, $violations);
    }

    public function testBorneMaximaleValide(): void
    {
        $note = $this->createNoteValide();
        $note->setValeur(20.0);

        $violations = $this->validator->validate($note);
        $this->assertCount(0, $violations);
    }

    public function testValeurNonRenseignee(): void
    {
        // valeur est null par défaut
        $note = new Note();
        $violations = $this->validator->validate($note);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('La valeur de la note doit être spécifiée.', $messages);
    }

    public function testValeurInferieureAZero(): void
    {
        $note = $this->createNoteValide();
        $note->setValeur(-0.5);

        $violations = $this->validator->validate($note);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn ($m) => str_contains($m, '0') && str_contains($m, '20'));
        $this->assertNotEmpty($found);
    }

    public function testValeurSuperieureAVingt(): void
    {
        $note = $this->createNoteValide();
        $note->setValeur(20.5);

        $violations = $this->validator->validate($note);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $messages = array_map(fn ($v) => $v->getMessage(), iterator_to_array($violations));
        $found = array_filter($messages, fn ($m) => str_contains($m, '0') && str_contains($m, '20'));
        $this->assertNotEmpty($found);
    }
}
