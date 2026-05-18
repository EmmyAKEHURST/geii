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

#[Route('/espace/personnel/emplois-du-temps')]
#[IsGranted('ROLE_PERSONNEL')]
final class EmploiDuTempsController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EmploiDuTempsRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('', name: 'app_espace_personnel_edt', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/edt.html.twig', [
            'staff' => $this->getStaffData(),
            'schedules' => $this->repository->findBy([], ['date_heure_debut' => 'ASC']),
        ]);
    }

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

    #[Route('/{id}/delete', name: 'app_espace_personnel_edt_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(EmploiDuTemps $slot, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-edt-' . $slot->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($slot);
        $this->em->flush();
        $this->addFlash('success', 'Créneau supprimé.');

        return $this->redirectToRoute('app_espace_personnel_edt');
    }
}
