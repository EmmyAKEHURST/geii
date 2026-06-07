<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Tests\Integration\IntegrationTestCase;

class NoteIntegrationTest extends IntegrationTestCase
{
    public function testPersistanceNote(): void
    {
        $matiere = new Matiere();
        $matiere->setNom('Automatisme');
        $this->em->persist($matiere);

        $etudiant = new Etudiant();
        $etudiant->setNumEtudiant('ETU2024010');
        $etudiant->setNom('Petit');
        $etudiant->setPrenom('Sophie');
        $etudiant->setAnnee(2);
        $this->em->persist($etudiant);

        $note = new Note();
        $note->setValeur(16.5);
        $note->setMatiere($matiere);
        $note->setEtudiant($etudiant);
        $note->setCommentaire('Très bon travail.');
        $this->em->persist($note);

        $this->em->flush();

        $this->assertNotNull($note->getId());

        $this->em->clear();

        $trouve = $this->em->find(Note::class, $note->getId());
        $this->assertNotNull($trouve);
        $this->assertSame(16.5, $trouve->getValeur());
        $this->assertSame('Très bon travail.', $trouve->getCommentaire());
        $this->assertNotNull($trouve->getMatiere());
        $this->assertNotNull($trouve->getEtudiant());
    }

    public function testPersistanceNoteSansCommentaire(): void
    {
        $matiere = new Matiere();
        $matiere->setNom('Électronique');
        $this->em->persist($matiere);

        $etudiant = new Etudiant();
        $etudiant->setNumEtudiant('ETU2024011');
        $etudiant->setNom('Roux');
        $etudiant->setPrenom('Luc');
        $etudiant->setAnnee(1);
        $this->em->persist($etudiant);

        $note = new Note();
        $note->setValeur(10.0);
        $note->setMatiere($matiere);
        $note->setEtudiant($etudiant);
        $this->em->persist($note);

        $this->em->flush();

        $this->em->clear();

        $trouve = $this->em->find(Note::class, $note->getId());
        $this->assertNotNull($trouve);
        $this->assertNull($trouve->getCommentaire());
    }
}
