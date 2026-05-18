<?php

namespace App\Controller\Personnel;

use App\Entity\Etudiant;
use App\Form\Personnel\EtudiantType;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * CRUD des étudiants. La clé primaire est la chaîne `num_etudiant`.
 *
 * Quand on associe un Compte à un Etudiant, le rôle ROLE_ETUDIANT est
 * automatiquement ajouté au Compte (via le trait StaffContextTrait).
 */
#[Route('/espace/personnel/etudiants')]
#[IsGranted('ROLE_PERSONNEL')]
final class EtudiantController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EtudiantRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'app_espace_personnel_etudiants', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/etudiants.html.twig', [
            'staff' => $this->getStaffData(),
            'students' => $this->repository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_etudiants_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant, [
            'is_new' => true,
            'current_compte' => null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($etudiant->getCompte() !== null) {
                $this->addRoleToCompte($etudiant->getCompte(), 'ROLE_ETUDIANT');
            }

            $this->em->persist($etudiant);
            $this->em->flush();

            $this->addFlash('success', 'Étudiant créé.');

            return $this->redirectToRoute('app_espace_personnel_etudiants');
        }

        return $this->render('espace/personnel/etudiants_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'etudiant' => $etudiant,
            'isNew' => true,
        ]);
    }

    #[Route('/{numEtudiant}/edit', name: 'app_espace_personnel_etudiants_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity(mapping: ['numEtudiant' => 'num_etudiant'])] Etudiant $etudiant,
        Request $request,
    ): Response {
        $form = $this->createForm(EtudiantType::class, $etudiant, [
            'current_compte' => $etudiant->getCompte(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($etudiant->getCompte() !== null) {
                $this->addRoleToCompte($etudiant->getCompte(), 'ROLE_ETUDIANT');
            }

            $this->em->flush();

            $this->addFlash('success', 'Étudiant mis à jour.');

            return $this->redirectToRoute('app_espace_personnel_etudiants');
        }

        return $this->render('espace/personnel/etudiants_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'etudiant' => $etudiant,
            'isNew' => false,
        ]);
    }

    #[Route('/{numEtudiant}/delete', name: 'app_espace_personnel_etudiants_delete', methods: ['POST'])]
    public function delete(
        #[MapEntity(mapping: ['numEtudiant' => 'num_etudiant'])] Etudiant $etudiant,
        Request $request,
    ): Response {
        if (!$this->isCsrfTokenValid('delete-etudiant-' . $etudiant->getNumEtudiant(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($etudiant);
        $this->em->flush();

        $this->addFlash('success', 'Étudiant supprimé.');

        return $this->redirectToRoute('app_espace_personnel_etudiants');
    }
}
