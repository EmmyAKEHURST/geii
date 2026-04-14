<?php

namespace App\Entity;

use App\Repository\EmploiDuTempsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmploiDuTempsRepository::class)]
class EmploiDuTemps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $date_heure_debut = null;

    #[ORM\Column]
    private ?\DateTime $date_heure_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $salle = null;

    #[ORM\Column(length: 255)]
    private ?string $matiere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->date_heure_debut;
    }

    public function setDateHeureDebut(\DateTime $date_heure_debut): static
    {
        $this->date_heure_debut = $date_heure_debut;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTime
    {
        return $this->date_heure_fin;
    }

    public function setDateHeureFin(\DateTime $date_heure_fin): static
    {
        $this->date_heure_fin = $date_heure_fin;

        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(string $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(string $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }
}
