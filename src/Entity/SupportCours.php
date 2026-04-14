<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SupportCoursRepository;

#[ORM\Entity(repositoryClass: SupportCoursRepository::class)]
class SupportCours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $fichier_path = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_depot = null;

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

    public function getFichierPath(): ?string
    {
        return $this->fichier_path;
    }

    public function setFichierPath(string $fichier_path): static
    {
        $this->fichier_path = $fichier_path;

        return $this;
    }

    public function getDateDepot(): ?\DateTime
    {
        return $this->date_depot;
    }

    public function setDateDepot(\DateTime $date_depot): static
    {
        $this->date_depot = $date_depot;

        return $this;
    }
}
