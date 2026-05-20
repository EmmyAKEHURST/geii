<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Repository\CompteRepository;
use App\Repository\EnseignantRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\EtudiantRepository;
use App\Repository\MatiereRepository;
use App\Repository\OffreAlternanceRepository;
use App\Repository\ProjetTuteureRepository;
use App\Repository\SupportCoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Tableau de bord de l'Espace Personnel.
 *
 * Toutes les ressources CRUD sont gérées par des contrôleurs dédiés dans
 * App\Controller\Personnel\* afin de respecter le principe de responsabilité
 * unique (un contrôleur = une ressource), à l'image de ce que produit
 * `bin/console make:crud`.
 *
 * Accès réservé à ROLE_PERSONNEL (cf. config/packages/security.yaml +
 * IsGranted ci-dessous, double couche).
 */
#[Route('/espace/personnel')]
#[IsGranted('ROLE_PERSONNEL')]
final class EspacePersonnelController extends AbstractController
{
    /**
     * Tableau de bord — vue d'ensemble et raccourcis vers chaque rubrique.
     * Les compteurs sont récupérés en temps réel depuis chaque repository.
     */
    #[Route('/', name: 'app_espace_personnel_index')]
    public function index(
        CompteRepository          $compteRepository,
        EtudiantRepository        $etudiantRepository,
        EnseignantRepository      $enseignantRepository,
        EntrepriseRepository      $entrepriseRepository,
        MatiereRepository         $matiereRepository,
        OffreAlternanceRepository $offreAlternanceRepository,
        ProjetTuteureRepository   $projetTuteureRepository,
        SupportCoursRepository    $supportCoursRepository,
    ): Response
    {
        $stats = [
            [
                'label' => 'Comptes',
                'value' => $compteRepository->count(),
                'icon' => 'bi-people-fill',
                'variant' => 'comptes'
            ], [
                'label' => 'Étudiants',
                'value' => $etudiantRepository->count(),
                'icon' => 'bi-mortarboard',
                'variant' => 'etudiants'
            ], [
                'label' => 'Enseignants',
                'value' => $enseignantRepository->count(),
                'icon' => 'bi-person-video3',
                'variant' => 'enseignants'
            ], [
                'label' => 'Entreprises',
                'value' => $entrepriseRepository->count(),
                'icon' => 'bi-buildings',
                'variant' => 'entreprises'
            ], [
                'label' => 'Matières',
                'value' => $matiereRepository->count(),
                'icon' => 'bi-bookmark-star',
                'variant' => 'matieres'
            ], [
                'label' => 'Offres alternance',
                'value' => $offreAlternanceRepository->count(),
                'icon' => 'bi-briefcase',
                'variant' => 'alternance'
            ], [
                'label' => 'Projets tuteurés',
                'value' => $projetTuteureRepository->count(),
                'icon' => 'bi-kanban',
                'variant' => 'projets'
            ], [
                'label' => 'Supports',
                'value' => $supportCoursRepository->count(),
                'icon' => 'bi-file-earmark-pdf',
                'variant' => 'supports'
            ],
        ];

        return $this->render('espace/personnel/index.html.twig', [
            'staff' => $this->getStaffData(),
            'stats' => $stats,
        ]);
    }

    /**
     * Données affichées dans la sidebar pour l'utilisateur connecté.
     *
     * @return array{firstName: string, lastName: string, function: string}
     */
    private function getStaffData(): array
    {
        /** @var Compte|null $user */
        $user = $this->getUser();
        $email = $user?->getUserIdentifier() ?? 'personnel@geii.fr';

        return [
            'firstName' => 'Espace',
            'lastName' => 'Personnel',
            'function' => $email,
        ];
    }
}
