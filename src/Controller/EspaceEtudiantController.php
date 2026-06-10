<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\SupportCours;
use App\Enum\StatutAlternance;
use App\Repository\{EmploiDuTempsRepository, NoteRepository, OffreAlternanceRepository, ProjetTuteureRepository, SupportCoursRepository};
use DateTime;
use DateMalformedStringException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
    public function emploiDuTemps(Request $request): Response
    {
        $dayNames = [
            'Monday' => 'lundi', 'Tuesday' => 'mardi', 'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi', 'Friday' => 'vendredi', 'Saturday' => 'samedi', 'Sunday' => 'dimanche',
        ];

        $weekOffset = (int) $request->query->get('week', 0);

        $modifier = $weekOffset === 0 ? 'monday this week' : sprintf('monday this week %+d week', $weekOffset);
        $monday = (new DateTime())->modify($modifier)->setTime(0, 0);

        $monthNames = [
            1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
        ];

        $weekdays = [];
        for ($i = 0; $i < 5; $i++) {
            $day = (clone $monday)->modify("+$i days");
            $keys = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
            $labels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
            $weekdays[] = [
                'key' => $keys[$i],
                'label' => $labels[$i],
                'date' => (int) $day->format('j') . ' ' . $monthNames[(int) $day->format('n')],
            ];
        }

        /** @var Compte $compte */
        $compte = $this->getUser();

        return $this->render('espace/etudiant/edt.html.twig', [
            'etudiant' => $compte->getEtudiant(),
            'scheduleDay' => $weekOffset === 0 ? $dayNames[(new DateTime())->format('l')] : '',
            'scheduleHours' => range(9, 18),
            'weekdays' => $weekdays,
            'weeklyByDay' => $this->edtRepository->getWeeklyPlanning($weekOffset),
            'weekOffset' => $weekOffset,
            'weekStart' => $monday->format('d/m/Y'),
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
     * Télécharge un support de cours.
     */
    #[Route('/supports-cours/{id}/download', name: 'app_espace_etudiant_supports_cours_download', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function downloadSupport(SupportCours $support): BinaryFileResponse
    {
        /** @var string $projectDir */
        $projectDir = $this->getParameter('kernel.project_dir');
        $absolutePath = $projectDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'share' . DIRECTORY_SEPARATOR . 'supports' . DIRECTORY_SEPARATOR . $support->getFichierPath();

        if (!is_file($absolutePath)) {
            throw $this->createNotFoundException('Fichier introuvable.');
        }

        $response = new BinaryFileResponse($absolutePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $support->getTitre() . '.pdf'
        );

        return $response;
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
