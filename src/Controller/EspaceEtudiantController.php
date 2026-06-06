<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Enum\StatutAlternance;
use App\Repository\{EmploiDuTempsRepository, NoteRepository, OffreAlternanceRepository, ProjetTuteureRepository, SupportCoursRepository};
use DateTime;
use DateMalformedStringException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur qui représente un espace étudiant. Accessible uniquement aux étudiants.
 */
#[Route('/espace/etudiant')]
#[IsGranted("ROLE_ETUDIANT")]
final class EspaceEtudiantController extends AbstractController
{
    public function __construct(
        private readonly EmploiDuTempsRepository   $edtRepository,
        private readonly NoteRepository            $noteRepository,
        private readonly OffreAlternanceRepository $offreRepository,
        private readonly ProjetTuteureRepository   $projetRepository,
        private readonly SupportCoursRepository    $supportCoursRepository,
    ) {}

    /**
     * Représente l'accueil de l'espace étudiant.
     *
     * @throws DateMalformedStringException
     */
    #[Route('/', name: 'app_espace_etudiant_index')]
    public function index(): Response
    {
        $dayNames = [
            'Monday' => 'lundi', 'Tuesday' => 'mardi', 'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi', 'Friday' => 'vendredi', 'Saturday' => 'samedi', 'Sunday' => 'dimanche',
        ];

        /** @var Compte $compte */
        $compte = $this->getUser();
        $etudiant = $compte->getEtudiant();

        $notes = $this->noteRepository->findBy(['etudiant' => $etudiant]);
        $gradeStats = $this->computeGradeStats($notes);

        return $this->render('espace/etudiant/index.html.twig', [
            'etudiant' => $etudiant,
            'schedule' => $this->edtRepository->getPlanningForToday(),
            'scheduleDay' => $dayNames[(new DateTime())->format('l')],
            'scheduleHours' => range(9, 18),
            'notes' => $notes,
            'gradeStats' => $gradeStats,
            'offers' => $this->offreRepository->findBy(['statut' => StatutAlternance::ACTIVE]),
        ]);
    }

    /**
     * Représente l'emploi du temps hebdomadaire (du lundi au vendredi, de 9h à 18h).
     *
     * @throws DateMalformedStringException
     */
    #[Route('/emploi-du-temps', name: 'app_espace_etudiant_edt')]
    public function emploiDuTemps(): Response
    {
        $dayNames = [
            'Monday' => 'lundi', 'Tuesday' => 'mardi', 'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi', 'Friday' => 'vendredi', 'Saturday' => 'samedi', 'Sunday' => 'dimanche',
        ];

        /** @var Compte $compte */
        $compte = $this->getUser();

        return $this->render('espace/etudiant/edt.html.twig', [
            'etudiant' => $compte->getEtudiant(),
            'scheduleDay' => $dayNames[(new DateTime())->format('l')],
            'scheduleHours' => range(9, 18),
            'weekdays' => [
                ['key' => 'lundi', 'label' => 'Lundi'],
                ['key' => 'mardi', 'label' => 'Mardi'],
                ['key' => 'mercredi', 'label' => 'Mercredi'],
                ['key' => 'jeudi', 'label' => 'Jeudi'],
                ['key' => 'vendredi', 'label' => 'Vendredi'],
            ],
            'weeklyByDay' => $this->edtRepository->getWeeklyPlanning(),
        ]);
    }

    /**
     * Représente l'ensemble de notes.
     */
    #[Route('/notes', name: 'app_espace_etudiant_notes')]
    public function notes(): Response
    {
        /** @var Compte $compte */
        $compte = $this->getUser();
        $etudiant = $compte->getEtudiant();

        $notes = $this->noteRepository->findBy(['etudiant' => $etudiant]);

        return $this->render('espace/etudiant/notes.html.twig', [
            'etudiant' => $etudiant,
            'notes' => $notes,
            'gradeStats' => $this->computeGradeStats($notes),
        ]);
    }

    /**
     * Représente les offres d'alternance actives.
     */
    #[Route('/offres-alternance', name: 'app_espace_etudiant_offres_alternance')]
    public function offresAlternance(): Response
    {
        /** @var Compte $compte */
        $compte = $this->getUser();

        return $this->render('espace/etudiant/alternance.html.twig', [
            'etudiant' => $compte->getEtudiant(),
            'offers' => $this->offreRepository->findBy(['statut' => StatutAlternance::ACTIVE]),
        ]);
    }

    /**
     * Représente les projets tuteurés.
     */
    #[Route('/projets-tuteures', name: 'app_espace_etudiant_projets-tuteures')]
    public function projetsTuteures(): Response
    {
        /** @var Compte $compte */
        $compte = $this->getUser();

        return $this->render('espace/etudiant/projets.html.twig', [
            'etudiant' => $compte->getEtudiant(),
            'projects' => $this->projetRepository->findAll(),
        ]);
    }

    /**
     * Représente la liste des supports de cours disponibles.
     */
    #[Route('/supports-cours', name: 'app_espace_etudiant_supports_cours')]
    public function supportsCours(): Response
    {
        /** @var Compte $compte */
        $compte = $this->getUser();

        return $this->render('espace/etudiant/supports-cours.html.twig', [
            'etudiant' => $compte->getEtudiant(),
            'supports' => $this->supportCoursRepository->findBy([], ['date_depot' => 'DESC']),
        ]);
    }

    /**
     * Calcule la moyenne, la meilleure et la pire note à partir d'une liste de Note.
     *
     * @param array<\App\Entity\Note> $notes
     * @return array{average: float|null, best: float|null, worst: float|null}
     */
    private function computeGradeStats(array $notes): array
    {
        $values = array_map(fn($n) => $n->getValeur(), $notes);

        return count($values) > 0 ? [
            'average' => round(array_sum($values) / count($values), 2),
            'best' => max($values),
            'worst' => min($values),
        ] : ['average' => 0.0, 'best' => 0.0, 'worst' => 0.0];
    }
}
