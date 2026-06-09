<?php

namespace App\Controller\Personnel;

use App\Entity\EmploiDuTemps;
use App\Form\Personnel\EmploiDuTempsType;
use App\Repository\EmploiDuTempsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion de l'emploi du temps de l'établissement.
 *
 * Permet d'afficher, créer, modifier et supprimer les créneaux horaires
 * associés aux cours (EmploiDuTemps).
 */
#[Route('/espace/personnel/emplois-du-temps')]
#[IsGranted('ROLE_PERSONNEL')]
final class EmploiDuTempsController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EmploiDuTempsRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Affiche la liste complète des créneaux d'emploi du temps.
     *
     * @description Cette liste est triée par date et heure de début en ordre croissant.
     *
     * @return Response La page HTML de la liste des créneaux
     */
    #[Route('', name: 'app_espace_personnel_edt', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $weekOffset = (int) $request->query->get('week', 0);

        $modifier = $weekOffset === 0 ? 'monday this week' : sprintf('monday this week %+d week', $weekOffset);
        $monday = (new \DateTime())->modify($modifier)->setTime(0, 0);

        $monthNames = [
            1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
            5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
            9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
        ];

        return $this->render('espace/personnel/edt.html.twig', [
            'staff'      => $this->getStaffData(),
            'schedules'  => $this->repository->getWeekSchedules($weekOffset),
            'weekOffset' => $weekOffset,
            'weekStart'  => $monday->format('d/m/Y'),
            'weekLabel'  => (int) $monday->format('j') . ' ' . $monthNames[(int) $monday->format('n')] . ' ' . $monday->format('Y'),
        ]);
    }

    /**
     * Crée un nouveau créneau d'emploi du temps.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_edt_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $slot = new EmploiDuTemps();
        $form = $this->createForm(EmploiDuTempsType::class, $slot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($slot);
            $this->em->flush();

            $this->addFlash('success', 'Créneau créé.');

            return $this->redirectToRoute('app_espace_personnel_edt');
        }

        return $this->render('espace/personnel/edt_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'slot' => $slot,
            'isNew' => true,
        ]);
    }

    /**
     * Modifie un créneau d'emploi du temps existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     *
     * @param EmploiDuTemps $slot Le créneau à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_edt_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(EmploiDuTemps $slot, Request $request): Response
    {
        $form = $this->createForm(EmploiDuTempsType::class, $slot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Créneau mis à jour.');

            return $this->redirectToRoute('app_espace_personnel_edt');
        }

        return $this->render('espace/personnel/edt_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'slot' => $slot,
            'isNew' => false,
        ]);
    }

    /**
     * Supprime un créneau d'emploi du temps.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param EmploiDuTemps $slot Le créneau à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des créneaux
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_edt_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(EmploiDuTemps $slot, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-edt-' . $slot->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($slot);
        $this->em->flush();
        $this->addFlash('success', 'Créneau supprimé.');

        return $this->redirectToRoute('app_espace_personnel_edt');
    }
}
