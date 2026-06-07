<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[UniqueEntity(fields: ['compte'], message: 'Ce compte est déjà associé à une autre entreprise.', ignoreNull: true)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'entreprise ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le numéro SIRET ne peut pas être vide.')]
    #[Assert\Length(
        min: 14,
        max: 14,
        exactMessage: 'Le numéro SIRET doit contenir exactement {{ limit }} chiffres.'
    )]
    #[Assert\Regex(pattern: '/^\d{14}$/', message: 'Le numéro SIRET doit seulement contenir des chiffres.')]
    private ?string $siret = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le secteur d'activité ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: 'Le secteur ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $secteur = null;

    /**
     * Compte utilisateur associé (relation owning, FK unique).
     * onDelete=CASCADE : supprimer un Compte supprime l'Entreprise liée
     * (et donc, par transitivité, ses offres et projets).
     */
    #[ORM\OneToOne(inversedBy: 'entreprise', targetEntity: Compte::class)]
    #[ORM\JoinColumn(name: 'compte_id', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'CASCADE')]
    private ?Compte $compte = null;

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

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    /**
     * Met à jour le compte associé. Pas de synchronisation explicite côté Compte
     * pour éviter une récursion infinie avec Compte::setEntreprise().
     */
    public function setCompte(?Compte $compte): static
    {
        $this->compte = $compte;

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
