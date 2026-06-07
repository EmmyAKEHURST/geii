<?php

namespace App\Tests\Repository;

use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Tests\IntegrationTestCase;

class NoteRepositoryTest extends IntegrationTestCase
{
    private NoteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->em->getRepository(Note::class);
    }

    private function createEtudiant(string $num): Etudiant
    {
        $etudiant = new Etudiant();
        $etudiant->setNumEtudiant($num);
        $etudiant->setNom('Dupont');
        $etudiant->setPrenom('Alice');
        $etudiant->setAnnee(1);

        $this->em->persist($etudiant);

        return $etudiant;
    }

    private function createMatiere(string $nom): Matiere
    {
        $matiere = new Matiere();
        $matiere->setNom($nom);

        $this->em->persist($matiere);

        return $matiere;
    }

    private function createNote(float $valeur, Etudiant $etudiant, Matiere $matiere): Note
    {
        $note = new Note();
        $note->setValeur($valeur);
        $note->setEtudiant($etudiant);
        $note->setMatiere($matiere);

        $this->em->persist($note);

        return $note;
    }

    /**
     * Vérifie que find retourne null pour un identifiant inexistant.
     */
    public function testFindRetourneNullSiInexistant(): void
    {
        $this->assertNull($this->repository->find(99999));
    }

    /**
     * Vérifie qu'une note est retrouvable par son identifiant avec sa valeur.
     */
    public function testFindParId(): void
    {
        $etudiant = $this->createEtudiant('ETU001');
        $matiere = $this->createMatiere('Maths');
        $note = $this->createNote(14.0, $etudiant, $matiere);

        $this->em->flush();

        $trouve = $this->repository->find($note->getId());

        $this->assertNotNull($trouve);
        $this->assertSame(14.0, $trouve->getValeur());
    }

    /**
     * Vérifie que findAll retourne toutes les notes persistées.
     */
    public function testFindAll(): void
    {
        $etudiant = $this->createEtudiant('ETU001');
        $matiere = $this->createMatiere('Maths');

        $this->createNote(10.0, $etudiant, $matiere);
        $this->createNote(14.0, $etudiant, $matiere);
        $this->createNote(18.0, $etudiant, $matiere);

        $this->em->flush();

        $this->assertCount(3, $this->repository->findAll());
    }

    /**
     * Vérifie que findBy filtre les notes par étudiant.
     */
    public function testFindByEtudiant(): void
    {
        $etudiant1 = $this->createEtudiant('ETU001');
        $etudiant2 = $this->createEtudiant('ETU002');
        $matiere = $this->createMatiere('Physique');

        $this->createNote(12.0, $etudiant1, $matiere);
        $this->createNote(15.0, $etudiant1, $matiere);
        $this->createNote(9.0, $etudiant2, $matiere);
        $this->em->flush();

        $notesEtudiant1 = $this->repository->findBy(['etudiant' => $etudiant1]);
        $this->assertCount(2, $notesEtudiant1);

        $notesEtudiant2 = $this->repository->findBy(['etudiant' => $etudiant2]);
        $this->assertCount(1, $notesEtudiant2);
    }

    /**
     * Vérifie que findBy filtre les notes par matière.
     */
    public function testFindByMatiere(): void
    {
        $etudiant = $this->createEtudiant('ETU001');
        $maths = $this->createMatiere('Maths');
        $physique = $this->createMatiere('Physique');

        $this->createNote(12.0, $etudiant, $maths);
        $this->createNote(15.0, $etudiant, $physique);
        $this->em->flush();

        $this->assertCount(1, $this->repository->findBy(['matiere' => $maths]));
        $this->assertCount(1, $this->repository->findBy(['matiere' => $physique]));
    }
}
