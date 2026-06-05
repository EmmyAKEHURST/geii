<?php

namespace App\Controller;

use App\Controller\Entreprise\EntrepriseContextTrait;
use App\Repository\OffreAlternanceRepository;
use App\Repository\ProjetTuteureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Tableau de bord de l'Espace Entreprise (partenaires GEII).
 *
 * Droits CDC §2.2 : publier et gérer ses offres d'alternance et projets tuteurés.
 * Les CRUD détaillés sont dans App\Controller\Entreprise\*.
 */
#[Route('/espace/entreprise')]
#[IsGranted('ROLE_ENTREPRISE')]
final class EspaceEntrepriseController extends AbstractController
{
    use EntrepriseContextTrait;

    #[Route('/', name: 'app_espace_entreprise_index')]
    public function index(
        OffreAlternanceRepository $offreRepository,
        ProjetTuteureRepository $projetRepository,
    ): Response {
        $entreprise = $this->requireEntreprise();

        $stats = [
            [
                'label' => "Offres d'alternance",
                'value' => $offreRepository->countByEntreprise($entreprise),
                'icon' => 'bi-briefcase',
                'variant' => 'alternance',
                'route' => 'app_espace_entreprise_offres_alternance',
            ],
            [
                'label' => 'Projets tuteurés',
                'value' => $projetRepository->countByEntreprise($entreprise),
                'icon' => 'bi-diagram-3',
                'variant' => 'projets',
                'route' => 'app_espace_entreprise_projets_tuteures',
            ],
        ];

        return $this->render('espace/entreprise/index.html.twig', [
            'company' => $this->getCompanyContext(),
            'entreprise' => $entreprise,
            'stats' => $stats,
            'recentOffers' => array_slice($offreRepository->findByEntrepriseOrdered($entreprise), 0, 5),
            'recentProjects' => array_slice($projetRepository->findByEntrepriseOrdered($entreprise), 0, 5),
        ]);
    }
}
