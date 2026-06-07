<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La valeur de la note doit être spécifiée.')]
    #[Assert\Range(
        min: 0,
        max: 20,
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}.'
    )]
    private ?float $valeur = null;

    /**
     * Matière concernée par la note.
     * CASCADE : supprimer une Matière supprime toutes les notes associées.
     */
    #[ORM\ManyToOne(inversedBy: 'notes', targetEntity: Matiere::class)]
    #[ORM\JoinColumn(name: 'matiere_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Matiere $matiere = null;

    /**
     * Étudiant concerné par la note.
     * CASCADE : supprimer un Étudiant supprime toutes ses notes.
     */
    #[ORM\ManyToOne(inversedBy: 'notes', targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'etudiant_id', referencedColumnName: 'num_etudiant', nullable: true, onDelete: 'CASCADE')]
    private ?Etudiant $etudiant = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
