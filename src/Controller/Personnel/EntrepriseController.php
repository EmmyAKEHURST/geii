<?php

namespace App\Controller\Personnel;

use App\Entity\Entreprise;
use App\Form\Personnel\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/personnel/entreprises')]
#[IsGranted('ROLE_PERSONNEL')]
final class EntrepriseController extends AbstractController
{
    use StaffContextTrait;

    #[Route('', name: 'app_espace_personnel_entreprises', methods: ['GET'])]
    public function index(EntrepriseRepository $repository): Response
    {
        return $this->render('espace/personnel/entreprises.html.twig', [
            'staff' => $this->getStaffData(),
            'companies' => $repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_entreprises_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entreprise);
            $em->flush();
            $this->addFlash('success', 'Entreprise créée.');

            return $this->redirectToRoute('app_espace_personnel_entreprises');
        }

        return $this->render('espace/personnel/entreprises_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'entreprise' => $entreprise,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_entreprises_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Entreprise $entreprise, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Entreprise mise à jour.');

            return $this->redirectToRoute('app_espace_personnel_entreprises');
        }

        return $this->render('espace/personnel/entreprises_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'entreprise' => $entreprise,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_entreprises_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Entreprise $entreprise, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-entreprise-' . $entreprise->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $em->remove($entreprise);
        $em->flush();
        $this->addFlash('success', 'Entreprise supprimée.');

        return $this->redirectToRoute('app_espace_personnel_entreprises');
    }
}
