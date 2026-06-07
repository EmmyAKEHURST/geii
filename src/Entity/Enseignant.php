<?php

namespace App\Entity;

use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnseignantRepository::class)]
#[UniqueEntity(fields: ['compte'], message: 'Ce compte est déjà associé à un autre enseignant.', ignoreNull: true)]
class Enseignant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La spécialité ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'La spécialité ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $specialite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le bureau ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le bureau ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $bureau = null;

    /**
     * Compte utilisateur associé (relation owning, FK unique).
     * onDelete=CASCADE : supprimer un Compte supprime l'Enseignant lié
     * (et donc, par transitivité, tous ses projets tuteurés).
     */
    #[ORM\OneToOne(inversedBy: 'enseignant', targetEntity: Compte::class)]
    #[ORM\JoinColumn(name: 'compte_id', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'CASCADE')]
    private ?Compte $compte = null;

    /**
     * Projets tuteurés dont cet enseignant est tuteur (côté inverse).
     *
     * @var Collection<int, ProjetTuteure>
     */
    #[ORM\OneToMany(mappedBy: 'enseignantTuteur', targetEntity: ProjetTuteure::class)]
    private Collection $projetsTutores;

    public function __construct()
    {
        $this->projetsTutores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getBureau(): ?string
    {
        return $this->bureau;
    }

    public function setBureau(string $bureau): static
    {
        $this->bureau = $bureau;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): static
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * @return Collection<int, ProjetTuteure>
     */
    public function getProjetsTutores(): Collection
    {
        return $this->projetsTutores;
    }

    public function addProjetTutore(ProjetTuteure $projet): static
    {
        if (!$this->projetsTutores->contains($projet)) {
            $this->projetsTutores->add($projet);
            $projet->setEnseignantTuteur($this);
        }

        return $this;
    }

    public function removeProjetTutore(ProjetTuteure $projet): static
    {
        if ($this->projetsTutores->removeElement($projet)) {
            if ($projet->getEnseignantTuteur() === $this) {
                $projet->setEnseignantTuteur(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return trim(($this->nom ?? '') . ' ' . ($this->prenom ?? ''));
    }
}
