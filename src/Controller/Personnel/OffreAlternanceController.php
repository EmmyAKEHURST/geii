<?php

namespace App\Controller\Personnel;

use App\Entity\OffreAlternance;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Personnel\OffreAlternanceType;
use App\Repository\OffreAlternanceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/espace/personnel/offres-alternance')]
#[IsGranted('ROLE_PERSONNEL')]
final class OffreAlternanceController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly OffreAlternanceRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('', name: 'app_espace_personnel_offres_alternance', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/offres-alternance.html.twig', [
            'staff' => $this->getStaffData(),
            'offers' => $this->repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_offres_alternance_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $offer = new OffreAlternance();
        $form = $this->createForm(OffreAlternanceType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($offer);
            $this->em->flush();

            $this->addFlash('success', 'Offre publiée.');

            return $this->redirectToRoute('app_espace_personnel_offres_alternance');
        }

        return $this->render('espace/personnel/offres-alternance_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'offer' => $offer,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_offres_alternance_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(OffreAlternance $offer, Request $request): Response
    {
        $form = $this->createForm(OffreAlternanceType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Offre mise à jour.');

            return $this->redirectToRoute('app_espace_personnel_offres_alternance');
        }

        return $this->render('espace/personnel/offres-alternance_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'offer' => $offer,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_offres_alternance_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(OffreAlternance $offer, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-offre-' . $offer->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($offer);
        $this->em->flush();

        $this->addFlash('success', 'Offre supprimée.');

        return $this->redirectToRoute('app_espace_personnel_offres_alternance');
    }
}
