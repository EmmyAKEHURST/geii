<?php

namespace App\Controller\Enseignant;

use DateTime;
use DateMalformedStringException;
use App\Repository\EmploiDuTempsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
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

        $weekOffset = (int) $request->query->get('week', 0);

        $modifier = $weekOffset === 0 ? 'monday this week' : sprintf('monday this week %+d week', $weekOffset);
        $monday = (new DateTime())->modify($modifier)->setTime(0, 0);

        $monthNames = [
            1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
        ];

        $weekdays = [];
        $keys   = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
        $labels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        for ($i = 0; $i < 5; $i++) {
            $day = (clone $monday)->modify("+$i days");
            $weekdays[] = [
                'key'   => $keys[$i],
                'label' => $labels[$i],
                'date'  => (int) $day->format('j') . ' ' . $monthNames[(int) $day->format('n')],
            ];
        }

        return $this->render('espace/enseignant/edt.html.twig', [
            'teacher'       => $this->getTeacherData(),
            'scheduleDay'   => $weekOffset === 0 ? $dayNames[(new DateTime())->format('l')] : '',
            'scheduleHours' => range(9, 18),
            'weekdays'      => $weekdays,
            'weeklyByDay'   => $this->repository->getWeeklyPlanning($weekOffset),
            'weekOffset'    => $weekOffset,
            'weekStart'     => $monday->format('d/m/Y'),
        ]);
    }
}
