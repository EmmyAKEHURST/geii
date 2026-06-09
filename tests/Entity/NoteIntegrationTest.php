<?php

namespace App\Tests\Entity;

use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Tests\IntegrationTestCase;

class NoteIntegrationTest extends IntegrationTestCase
{
    /**
     * Vérifie qu'une note est persistée avec sa valeur, son commentaire et ses relations matière/étudiant.
     */
    public function testPersistanceNote(): void
    {
        $matiere = (new Matiere())->setNom('Automatisme');

        $this->em->persist($matiere);

        $etudiant = (new Etudiant())
            ->setNumEtudiant('ETU2024010')
            ->setNom('Petit')
            ->setPrenom('Sophie')
            ->setAnnee(2)
        ;

        $this->em->persist($etudiant);

        $note = (new Note())
            ->setValeur(16.5)
            ->setMatiere($matiere)
            ->setEtudiant($etudiant)
            ->setCommentaire('Très bon travail.')
        ;

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

    /**
     * Vérifie qu'une note peut être persistée sans commentaire (champ nullable).
     */
    public function testPersistanceNoteSansCommentaire(): void
    {
        $matiere = (new Matiere())->setNom('Électronique');

        $this->em->persist($matiere);

        $etudiant = (new Etudiant())
            ->setNumEtudiant('ETU2024011')
            ->setNom('Roux')
            ->setPrenom('Luc')
            ->setAnnee(1)
        ;

        $this->em->persist($etudiant);

        $note = (new Note())
            ->setValeur(10.0)
            ->setMatiere($matiere)
            ->setEtudiant($etudiant)
        ;

        $this->em->persist($note);
        $this->em->flush();
        $this->em->clear();

        $trouve = $this->em->find(Note::class, $note->getId());

        $this->assertNotNull($trouve);
        $this->assertNull($trouve->getCommentaire());
    }
}
