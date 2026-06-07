<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EmploiDuTempsRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmploiDuTempsRepository::class)]
class EmploiDuTemps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date et heure de début doit être spécifiée.')]
    private ?\DateTime $date_heure_debut = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La date et heure de fin doit être spécifiée.')]
    #[Assert\GreaterThan(propertyPath: 'date_heure_debut', message: 'La date de fin doit être postérieure à la date de début.')]
    private ?\DateTime $date_heure_fin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La salle ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom de la salle ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $salle = null;

    #[ORM\ManyToOne(inversedBy: 'emploiDuTemps')]
    private ?Matiere $matiere = null;

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

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }
}
