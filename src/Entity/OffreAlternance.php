<?php

namespace App\Entity;

use App\Enum\StatutAlternance;
use App\Repository\OffreAlternanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreAlternanceRepository::class)]
class OffreAlternance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(enumType: StatutAlternance::class)]
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
