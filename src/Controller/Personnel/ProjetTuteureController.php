<?php

namespace App\Controller\Personnel;

use App\Entity\ProjetTuteure;
use App\Form\Personnel\ProjetTuteureType;
use App\Repository\ProjetTuteureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/personnel/projets-tuteures')]
#[IsGranted('ROLE_PERSONNEL')]
final class ProjetTuteureController extends AbstractController
{
    use StaffContextTrait;

    #[Route('', name: 'app_espace_personnel_projets_tuteures', methods: ['GET'])]
    public function index(ProjetTuteureRepository $repository): Response
    {
        return $this->render('espace/personnel/projets-tuteures.html.twig', [
            'staff' => $this->getStaffData(),
            'projects' => $repository->findBy([], ['annee' => 'DESC', 'titre' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_projets_tuteures_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $project = new ProjetTuteure();
        $form = $this->createForm(ProjetTuteureType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'Projet créé.');

            return $this->redirectToRoute('app_espace_personnel_projets_tuteures');
        }

        return $this->render('espace/personnel/projets-tuteures_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'project' => $project,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_projets_tuteures_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(ProjetTuteure $project, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProjetTuteureType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Projet mis à jour.');

            return $this->redirectToRoute('app_espace_personnel_projets_tuteures');
        }

        return $this->render('espace/personnel/projets-tuteures_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'project' => $project,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_projets_tuteures_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(ProjetTuteure $project, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-projet-' . $project->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $em->remove($project);
        $em->flush();
        $this->addFlash('success', 'Projet supprimé.');

        return $this->redirectToRoute('app_espace_personnel_projets_tuteures');
    }
}
