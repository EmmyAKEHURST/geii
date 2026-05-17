<?php

namespace App\Controller\Personnel;

use App\Entity\Matiere;
use App\Form\Personnel\MatiereType;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/personnel/matieres')]
#[IsGranted('ROLE_PERSONNEL')]
final class MatiereController extends AbstractController
{
    use StaffContextTrait;

    #[Route('', name: 'app_espace_personnel_matieres', methods: ['GET'])]
    public function index(MatiereRepository $repository): Response
    {
        return $this->render('espace/personnel/matieres.html.twig', [
            'staff' => $this->getStaffData(),
            'subjects' => $repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_matieres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($matiere);
            $em->flush();
            $this->addFlash('success', 'Matière créée.');

            return $this->redirectToRoute('app_espace_personnel_matieres');
        }

        return $this->render('espace/personnel/matieres_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'matiere' => $matiere,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_matieres_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Matiere $matiere, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Matière mise à jour.');

            return $this->redirectToRoute('app_espace_personnel_matieres');
        }

        return $this->render('espace/personnel/matieres_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'matiere' => $matiere,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_matieres_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Matiere $matiere, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-matiere-' . $matiere->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $em->remove($matiere);
        $em->flush();
        $this->addFlash('success', 'Matière supprimée.');

        return $this->redirectToRoute('app_espace_personnel_matieres');
    }
}
