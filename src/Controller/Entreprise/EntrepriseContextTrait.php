<?php

namespace App\Controller\Entreprise;

use App\Entity\Compte;
use App\Entity\Entreprise;
use App\Entity\OffreAlternance;
use App\Entity\ProjetTuteure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Contexte partagé des contrôleurs de l'Espace Entreprise.
 *
 * @phpstan-require-extends AbstractController
 */
trait EntrepriseContextTrait
{
    /**
     * @return array{companyName: string, sector: string, email: string}
     */
    private function getCompanyContext(): array
    {
        $entreprise = $this->requireEntreprise();
        /** @var Compte|null $user */
        $user = $this->getUser();

        return [
            'companyName' => $entreprise->getNom() ?? 'Entreprise',
            'sector' => $entreprise->getSecteur() ?? '',
            'email' => $user?->getUserIdentifier() ?? '',
        ];
    }

    private function requireEntreprise(): Entreprise
    {
        /** @var Compte|null $user */
        $user = $this->getUser();
        $entreprise = $user?->getEntreprise();

        if (!$entreprise instanceof Entreprise) {
            throw new AccessDeniedHttpException('Aucune entreprise n\'est associée à votre compte.');
        }

        return $entreprise;
    }

    private function assertOfferOwnedByEntreprise(OffreAlternance $offer, Entreprise $entreprise): void
    {
        if ($offer->getEntreprise()?->getId() !== $entreprise->getId()) {
            throw $this->createAccessDeniedException('Cette offre ne vous appartient pas.');
        }
    }

    private function assertProjectOwnedByEntreprise(ProjetTuteure $project, Entreprise $entreprise): void
    {
        if ($project->getEntreprise()?->getId() !== $entreprise->getId()) {
            throw $this->createAccessDeniedException('Ce projet ne vous appartient pas.');
        }
    }
}
