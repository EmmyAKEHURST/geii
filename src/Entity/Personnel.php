<?php

namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
#[UniqueEntity(fields: ['compte'], message: 'Ce compte est déjà associé à un autre membre du personnel.', ignoreNull: true)]
class Personnel
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
    #[Assert\NotBlank(message: 'La fonction ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'La fonction ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $fonction = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Le statut administrateur doit être renseigné.')]
    private ?bool $admin = null;

    /**
     * Compte utilisateur associé (relation owning, FK unique).
     * onDelete=CASCADE : supprimer un Compte supprime le Personnel lié.
     */
    #[ORM\OneToOne(inversedBy: 'personnel', targetEntity: Compte::class)]
    #[ORM\JoinColumn(name: 'compte_id', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'CASCADE')]
    private ?Compte $compte = null;

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

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): static
    {
        $this->admin = $admin;

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

    public function __toString(): string
    {
        return trim(($this->nom ?? '') . ' ' . ($this->prenom ?? ''));
    }
}
