<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * @var Collection<int, EmploiDuTemps>
     */
    #[ORM\OneToMany(targetEntity: EmploiDuTemps::class, mappedBy: 'matiere')]
    private Collection $emploiDuTemps;

    public function __construct()
    {
        $this->emploiDuTemps = new ArrayCollection();
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

    /**
     * @return Collection<int, EmploiDuTemps>
     */
    public function getEmploiDuTemps(): Collection
    {
        return $this->emploiDuTemps;
    }

    /**
     * @param EmploiDuTemps $emploiDuTemps
     *
     * @return $this
     */
    public function addEmploiDuTemps(EmploiDuTemps $emploiDuTemps): static
    {
        if (!$this->emploiDuTemps->contains($emploiDuTemps)) {
            $this->emploiDuTemps->add($emploiDuTemps);
            $emploiDuTemps->setMatiere($this);
        }

        return $this;
    }

    /**
     * @param EmploiDuTemps $emploiDuTemps
     *
     * @return $this
     */
    public function removeEmploiDuTemps(EmploiDuTemps $emploiDuTemps): static
    {
        if ($this->emploiDuTemps->removeElement($emploiDuTemps)) {
            if ($emploiDuTemps->getMatiere() === $this) {
                $emploiDuTemps->setMatiere(null);
            }
        }

        return $this;
    }
}
