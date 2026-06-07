<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
#[UniqueEntity(fields: ['compte'], message: 'Ce compte est déjà associé à un autre étudiant.', ignoreNull: true)]
class Etudiant
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le numéro étudiant ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le numéro étudiant ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $num_etudiant = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L'année doit être spécifiée.")]
    #[Assert\Positive(message: "L'année doit être un entier positif.")]
    private ?int $annee = null;

    /**
     * Compte utilisateur associé (relation owning, FK unique).
     * onDelete=CASCADE : supprimer un Compte supprime l'Étudiant lié
     * (et donc, par transitivité, toutes ses notes).
     */
    #[ORM\OneToOne(inversedBy: 'etudiant', targetEntity: Compte::class)]
    #[ORM\JoinColumn(name: 'compte_id', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'CASCADE')]
    private ?Compte $compte = null;

    /**
     * Notes rattachées à cet étudiant (côté inverse).
     *
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getNumEtudiant(): ?string
    {
        return $this->num_etudiant;
    }

    public function setNumEtudiant(string $num_etudiant): static
    {
        $this->num_etudiant = $num_etudiant;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    /**
     * Met à jour le compte associé. Pas de synchronisation explicite côté Compte
     * pour éviter une récursion : la propriété inverse Compte::etudiant est
     * automatiquement chargée par Doctrine.
     */
    public function setCompte(?Compte $compte): static
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setEtudiant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getEtudiant() === $this) {
                $note->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * Représentation lisible utilisée notamment par les EntityType (choice_label).
     */
    public function __toString(): string
    {
        return trim(($this->nom ?? '') . ' ' . ($this->prenom ?? '')) . ' (' . ($this->num_etudiant ?? '?') . ')';
    }
}
