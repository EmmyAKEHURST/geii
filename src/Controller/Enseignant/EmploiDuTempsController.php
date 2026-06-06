<?php

namespace App\Controller\Enseignant;

use DateTime;
use DateMalformedStringException;
use App\Repository\EmploiDuTempsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Consultation de l'emploi du temps depuis l'espace enseignant.
 *
 * Affiche le planning hebdomadaire en lecture seule.
 * Accès réservé à ROLE_ENSEIGNANT.
 */
#[Route('/espace/enseignant/emploi-du-temps')]
#[IsGranted('ROLE_ENSEIGNANT')]
final class EmploiDuTempsController extends AbstractController
{
    use TeacherContextTrait;

    public function __construct(
        private readonly EmploiDuTempsRepository $repository,
    ) {}

    /**
     * Affiche l'emploi du temps hebdomadaire (du lundi au vendredi, de 9h à 18h).
     *
     * @throws DateMalformedStringException
     */
    #[Route('', name: 'app_espace_enseignant_edt', methods: ['GET'])]
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

        return $this->render('espace/enseignant/edt.html.twig', [
            'teacher'      => $this->getTeacherData(),
            'scheduleDay'  => $dayNames[(new DateTime())->format('l')],
            'scheduleHours' => range(9, 18),
            'weekdays'     => [
                ['key' => 'lundi',    'label' => 'Lundi'],
                ['key' => 'mardi',    'label' => 'Mardi'],
                ['key' => 'mercredi', 'label' => 'Mercredi'],
                ['key' => 'jeudi',    'label' => 'Jeudi'],
                ['key' => 'vendredi', 'label' => 'Vendredi'],
            ],
            'weeklyByDay'  => $this->repository->getWeeklyPlanning(),
        ]);
    }
}
