<?php

namespace App\Controller\Personnel;

use App\Entity\Personnel;
use App\Form\Personnel\PersonnelType;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/espace/personnel/personnels')]
#[IsGranted('ROLE_PERSONNEL')]
final class PersonnelController extends AbstractController
{
    use StaffContextTrait;

    #[Route('', name: 'app_espace_personnel_personnels', methods: ['GET'])]
    public function index(PersonnelRepository $repository): Response
    {
        return $this->render('espace/personnel/personnels.html.twig', [
            'staff' => $this->getStaffData(),
            'personnels' => $repository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_personnels_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel, ['current_compte' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($personnel->getCompte() !== null) {
                $this->addRoleToCompte($personnel->getCompte(), 'ROLE_PERSONNEL');
            }
            $em->persist($personnel);
            $em->flush();
            $this->addFlash('success', 'Membre du personnel créé.');

            return $this->redirectToRoute('app_espace_personnel_personnels');
        }

        return $this->render('espace/personnel/personnels_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'personnel' => $personnel,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_personnels_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Personnel $personnel, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PersonnelType::class, $personnel, [
            'current_compte' => $personnel->getCompte(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($personnel->getCompte() !== null) {
                $this->addRoleToCompte($personnel->getCompte(), 'ROLE_PERSONNEL');
            }
            $em->flush();
            $this->addFlash('success', 'Membre du personnel mis à jour.');

            return $this->redirectToRoute('app_espace_personnel_personnels');
        }

        return $this->render('espace/personnel/personnels_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'personnel' => $personnel,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_personnels_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Personnel $personnel, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-personnel-' . $personnel->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $em->remove($personnel);
        $em->flush();
        $this->addFlash('success', 'Membre du personnel supprimé.');

        return $this->redirectToRoute('app_espace_personnel_personnels');
    }
}
