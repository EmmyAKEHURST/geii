<?php

namespace App\Controller\Enseignant;

use DateTime;
use DateMalformedStringException;
use App\Repository\NoteRepository;
use App\Repository\SupportCoursRepository;
use App\Repository\EmploiDuTempsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Tableau de bord de l'Espace Enseignant.
 *
 * Les ressources CRUD sont gérées par des contrôleurs dédiés dans
 * App\Controller\Enseignant\* afin de respecter le principe de responsabilité unique.
 *
 * Accès réservé à ROLE_ENSEIGNANT (cf. config/packages/security.yaml + IsGranted ci-dessous).
 */
#[Route('/espace/enseignant')]
#[IsGranted('ROLE_ENSEIGNANT')]
final class EspaceEnseignantController extends AbstractController
{
    use TeacherContextTrait;

    /**
     * Tableau de bord — vue d'ensemble et raccourcis vers chaque rubrique.
     *
     * @throws DateMalformedStringException
     */
    #[Route('/', name: 'app_espace_enseignant_index')]
    public function index(
        NoteRepository          $noteRepository,
        SupportCoursRepository  $supportCoursRepository,
        EmploiDuTempsRepository $edtRepository,
    ): Response {
        $dayNames = [
            'Monday' => 'lundi', 'Tuesday' => 'mardi', 'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi', 'Friday' => 'vendredi', 'Saturday' => 'samedi', 'Sunday' => 'dimanche',
        ];

        $stats = [
            [
                'label'   => 'Notes',
                'value'   => $noteRepository->count(),
                'icon'    => 'bi-clipboard2-check',
                'variant' => 'notes',
            ],
            [
                'label'   => 'Supports de cours',
                'value'   => $supportCoursRepository->count(),
                'icon'    => 'bi-file-earmark-pdf',
                'variant' => 'supports',
            ],
        ];

        return $this->render('espace/enseignant/index.html.twig', [
            'teacher'       => $this->getTeacherData(),
            'stats'         => $stats,
            'schedule'      => $edtRepository->getPlanningForToday(),
            'scheduleDay'   => $dayNames[(new DateTime())->format('l')],
            'scheduleHours' => range(9, 18),
        ]);
    }
}
