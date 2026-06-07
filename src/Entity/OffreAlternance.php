<?php

namespace App\Entity;

use App\Enum\StatutAlternance;
use App\Repository\OffreAlternanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreAlternanceRepository::class)]
class OffreAlternance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre de l'offre ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La durée doit être spécifiée.')]
    #[Assert\Positive(message: 'La durée doit être un entier positif (en mois).')]
    private ?int $duree = null;

    #[ORM\Column(enumType: StatutAlternance::class)]
    #[Assert\NotNull(message: 'Le statut doit être spécifié.')]
    private ?StatutAlternance $statut = null;

    /**
     * Entreprise qui publie l'offre.
     * CASCADE : supprimer une Entreprise supprime ses offres.
     */
    #[ORM\ManyToOne(inversedBy: 'offresAlternance', targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(name: 'entreprise_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Entreprise $entreprise = null;

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getStatut(): ?StatutAlternance
    {
        return $this->statut;
    }

    public function setStatut(StatutAlternance $statut): static
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
}
