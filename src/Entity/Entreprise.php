<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $siret = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $secteur = null;

    /**
     * Offres d'alternance publiées par cette entreprise (côté inverse).
     *
     * @var Collection<int, OffreAlternance>
     */
    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: OffreAlternance::class)]
    private Collection $offresAlternance;

    /**
     * Projets tuteurés commandités par cette entreprise (côté inverse).
     *
     * @var Collection<int, ProjetTuteure>
     */
    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: ProjetTuteure::class)]
    private Collection $projetsTutores;

    public function __construct()
    {
        $this->offresAlternance = new ArrayCollection();
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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(string $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * @return Collection<int, OffreAlternance>
     */
    public function getOffresAlternance(): Collection
    {
        return $this->offresAlternance;
    }

    public function addOffreAlternance(OffreAlternance $offre): static
    {
        if (!$this->offresAlternance->contains($offre)) {
            $this->offresAlternance->add($offre);
            $offre->setEntreprise($this);
        }

        return $this;
    }

    public function removeOffreAlternance(OffreAlternance $offre): static
    {
        if ($this->offresAlternance->removeElement($offre)) {
            if ($offre->getEntreprise() === $this) {
                $offre->setEntreprise(null);
            }
        }

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
            $projet->setEntreprise($this);
        }

        return $this;
    }

    public function removeProjetTutore(ProjetTuteure $projet): static
    {
        if ($this->projetsTutores->removeElement($projet)) {
            if ($projet->getEntreprise() === $this) {
                $projet->setEntreprise(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}
