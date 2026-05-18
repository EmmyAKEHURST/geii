<?php

namespace App\Controller\Personnel;

use App\Entity\Enseignant;
use App\Form\Personnel\EnseignantType;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/espace/personnel/enseignants')]
#[IsGranted('ROLE_PERSONNEL')]
final class EnseignantController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EnseignantRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('', name: 'app_espace_personnel_enseignants', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/enseignants.html.twig', [
            'staff' => $this->getStaffData(),
            'teachers' => $this->repository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_enseignants_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantType::class, $enseignant, ['current_compte' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($enseignant->getCompte() !== null) {
                $this->addRoleToCompte($enseignant->getCompte(), 'ROLE_ENSEIGNANT');
            }

            $this->em->persist($enseignant);
            $this->em->flush();

            $this->addFlash('success', 'Enseignant créé.');

            return $this->redirectToRoute('app_espace_personnel_enseignants');
        }

        return $this->render('espace/personnel/enseignants_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'enseignant' => $enseignant,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_enseignants_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Enseignant $enseignant, Request $request): Response
    {
        $form = $this->createForm(EnseignantType::class, $enseignant, [
            'current_compte' => $enseignant->getCompte(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($enseignant->getCompte() !== null) {
                $this->addRoleToCompte($enseignant->getCompte(), 'ROLE_ENSEIGNANT');
            }

            $this->em->flush();

            $this->addFlash('success', 'Enseignant mis à jour.');

            return $this->redirectToRoute('app_espace_personnel_enseignants');
        }

        return $this->render('espace/personnel/enseignants_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'enseignant' => $enseignant,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_enseignants_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Enseignant $enseignant, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-enseignant-' . $enseignant->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($enseignant);
        $this->em->flush();

        $this->addFlash('success', 'Enseignant supprimé.');

        return $this->redirectToRoute('app_espace_personnel_enseignants');
    }
}
