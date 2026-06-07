<?php

namespace App\Entity;

use App\Enum\StatutProjetTuteure;
use App\Repository\ProjetTuteureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjetTuteureRepository::class)]
class ProjetTuteure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "L'année doit être spécifiée.")]
    #[Assert\Positive(message: "L'année doit être un entier positif.")]
    private ?int $annee = null;

    #[ORM\Column(enumType: StatutProjetTuteure::class)]
    #[Assert\NotNull(message: 'Le statut doit être spécifié.')]
    private ?StatutProjetTuteure $statut = null;

    /**
     * Entreprise commanditaire du projet.
     * CASCADE : supprimer une Entreprise supprime ses projets.
     */
    #[ORM\ManyToOne(inversedBy: 'projetsTutores', targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(name: 'entreprise_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Entreprise $entreprise = null;

    /**
     * Enseignant tuteur en charge du projet.
     * CASCADE : supprimer un Enseignant supprime les projets dont il est tuteur.
     */
    #[ORM\ManyToOne(inversedBy: 'projetsTutores', targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'enseignant_tuteur_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Enseignant $enseignantTuteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getStatut(): ?StatutProjetTuteure
    {
        return $this->statut;
    }

    public function setStatut(StatutProjetTuteure $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getEnseignantTuteur(): ?Enseignant
    {
        return $this->enseignantTuteur;
    }

    public function setEnseignantTuteur(?Enseignant $enseignantTuteur): static
    {
        $this->enseignantTuteur = $enseignantTuteur;

        return $this;
    }
}
