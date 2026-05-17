<?php

namespace App\Controller;

use DateTime;
use DateMalformedStringException;
use App\Repository\EmploiDuTempsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur qui représente un espace étudiant. Accessible uniquement aux étudiants.
 */
#[Route('/espace/etudiant')]
final class EspaceEtudiantController extends AbstractController
{
    public function __construct(
        private readonly EmploiDuTempsRepository $edtRepository
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
            'Monday' => 'lundi',
            'Tuesday' => 'mardi',
            'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi',
            'Friday' => 'vendredi',
            'Saturday' => 'samedi',
            'Sunday' => 'dimanche',
        ];

        $todayKey = $dayNames[(new DateTime())->format('l')];

        $schedule = $this->edtRepository->getPlanningForToday();

        $grades = $this->gradesData();
        $gradeValues = array_column($grades, 'grade');

        $gradeStats = count($gradeValues) > 0 ? [
            'average' => round(array_sum($gradeValues) / count($gradeValues), 2),
            'best' => max($gradeValues),
            'worst' => min($gradeValues),
        ] : [
            'average' => 0,
            'best' => 0,
            'worst' => 0
        ];

        return $this->render('espace/etudiant/index.html.twig', [
            'student' => $this->studentData(),
            'schedule' => $schedule,
            'scheduleDay' => $todayKey,
            'scheduleHours' => range(9, 18),
            'grades' => $grades,
            'gradeStats' => $gradeStats,
            'offers' => $this->offersData(),
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

        $weeklyByDay = $this->edtRepository->getWeeklyPlanning();

        return $this->render('espace/etudiant/edt.html.twig', [
            'student' => $this->studentData(),
            'scheduleDay' => $dayNames[(new DateTime())->format('l')],
            'scheduleHours' => range(9, 18),
            'weekdays' => [
                ['key' => 'lundi', 'label' => 'Lundi'],
                ['key' => 'mardi', 'label' => 'Mardi'],
                ['key' => 'mercredi', 'label' => 'Mercredi'],
                ['key' => 'jeudi', 'label' => 'Jeudi'],
                ['key' => 'vendredi', 'label' => 'Vendredi'],
            ],
            'weeklyByDay' => $weeklyByDay,
        ]);
    }

    /**
     * Représente l'ensemble de notes.
     *
     * @return Response
     */
    #[Route('/notes', name: 'app_espace_etudiant_notes')]
    public function notes(): Response
    {
        $grades = $this->gradesData();
        $gradeValues = array_column($grades, 'grade');

        $gradeStats = count($gradeValues) > 0 ? [
            'average' => round(array_sum($gradeValues) / count($gradeValues), 2),
            'best' => max($gradeValues),
            'worst' => min($gradeValues),
        ] : [
            'average' => 0,
            'best' => 0,
            'worst' => 0
        ];

        return $this->render('espace/etudiant/notes.html.twig', [
            'student' => $this->studentData(),
            'grades' => $grades,
            'gradeStats' => $gradeStats,
        ]);
    }

    /**
     * Représente les offres d'alternance.
     *
     * @return Response
     */
    #[Route('/offres-alternance', name: 'app_espace_etudiant_offres_alternance')]
    public function offresAlternance(): Response
    {
        return $this->render('espace/etudiant/alternance.html.twig', [
            'student' => $this->studentData(),
            'offers' => $this->offersData(),
        ]);
    }

    /**
     * Représente les projets tuteurés.
     *
     * @return Response
     */
    #[Route('/projets-tuteures', name: 'app_espace_etudiant_projets-tuteures')]
    public function projetsTuteures(): Response
    {
        return $this->render('espace/etudiant/projets.html.twig', [
            'student' => $this->studentData(),
            'projects' => $this->projectsData(),
        ]);
    }

    /**
     * @description Les méthodes privées sont les fausses informations sur l'étudiant, les notes,
     * les offres d'alternance ainsi que les projets tuteurés. Elles seront supprimés au fur et à mesure.
     */

    /**
     * @return array<string, string>
     */
    private function studentData(): array
    {
        return [
            'firstName' => 'Thomas',
            'lastName' => 'LAMBERT',
            'promotion' => 'LP MIAW 2025-2026',
            'email' => 'thomas.lambert@iut.fr',
        ];
    }

    /**
     * @return list<array{subject: string, grade: float, teacher: string, date: string}>
     */
    private function gradesData(): array
    {
        return [
            ['subject' => 'Électronique Numérique', 'grade' => 15.50, 'teacher' => 'M. Dupont', 'date' => '2026-04-28'],
            ['subject' => 'Systèmes Embarqués', 'grade' => 13.00, 'teacher' => 'Mme Martin', 'date' => '2026-04-25'],
            ['subject' => 'Automatisme', 'grade' => 16.75, 'teacher' => 'M. Bernard', 'date' => '2026-04-22'],
            ['subject' => 'Mathématiques Appliquées', 'grade' => 12.50, 'teacher' => 'M. Petit', 'date' => '2026-04-18'],
            ['subject' => 'Communication Professionnelle', 'grade' => 14.00, 'teacher' => 'Mme Rousseau', 'date' => '2026-04-15'],
            ['subject' => 'Réseaux Industriels', 'grade' => 17.25, 'teacher' => 'M. Leroy', 'date' => '2026-04-10'],
        ];
    }

    /**
     * @return list<array{id: int, title: string, company: string, location: string, type: string, description: string}>
     */
    private function offersData(): array
    {
        return [
            ['id' => 1, 'title' => 'Développeur web', 'company' => 'Siemens France', 'location' => 'Lyon', 'type' => 'Alternance', 'description' => "Conception et maintenance de systèmes électroniques embarqués pour l'industrie automobile."],
            ['id' => 2, 'title' => 'Technicien Systèmes Embarqués', 'company' => 'Schneider Electric', 'location' => 'Grenoble', 'type' => 'Alternance', 'description' => 'Développement de firmware pour automates industriels et supervision de process.'],
            ['id' => 3, 'title' => 'Technicien Automaticien', 'company' => 'STMicroelectronics', 'location' => 'Crolles', 'type' => 'Alternance', 'description' => "Programmation d'automates et supervision de lignes de production semi-conducteurs."],
            ['id' => 4, 'title' => 'Ingénieur Électrotechnicien Junior', 'company' => 'EDF', 'location' => 'Grenoble', 'type' => 'Alternance', 'description' => 'Participation aux projets de modernisation des infrastructures électriques.'],
            ['id' => 5, 'title' => 'Développeur Systèmes Embarqués', 'company' => 'Thales', 'location' => 'Meylan', 'type' => 'Alternance', 'description' => 'Développement bas niveau pour systèmes avioniques et applications de défense.'],
            ['id' => 6, 'title' => 'Technicien Électronicien', 'company' => 'STMicro', 'location' => 'Crolles', 'type' => 'Alternance', 'description' => 'Tests et validation de circuits intégrés en salle blanche.'],
        ];
    }

    /**
     * @return list<array{title: string, description: string, status: string, progress: int, teacher: string}>
     */
    private function projectsData(): array
    {
        return [
            ['title' => 'Station Météo Connectée', 'description' => "Conception d'une station météo IoT avec capteurs DHT22 et transmission LoRa vers serveur local.", 'status' => 'En cours', 'progress' => 65, 'teacher' => 'M. Bernard'],
            ['title' => 'Banc de Test Automatisé', 'description' => "Développement d'un banc de test automatisé pour cartes électroniques en production avec interface HMI.", 'status' => 'En cours', 'progress' => 40, 'teacher' => 'Mme Martin'],
        ];
    }

}
