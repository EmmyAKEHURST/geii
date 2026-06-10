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

/**
 * Gestion du personnel de l'établissement.
 *
 * Permet d'afficher, créer, modifier et supprimer les membres du personnel.
 * Quand on associe un Compte à un Personnel, le rôle ROLE_PERSONNEL y est
 * automatiquement ajouté.
 */
#[Route('/espace/personnel/personnels')]
#[IsGranted('ROLE_PERSONNEL')]
final class PersonnelController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly PersonnelRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Affiche la liste complète du personnel.
     *
     * @description Cette liste est triée par nom et prénom en ordre croissant.
     *
     * @return Response La page HTML de la liste du personnel
     */
    #[Route('', name: 'app_espace_personnel_personnels', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/personnels.html.twig', [
            'staff' => $this->getStaffData(),
            'personnels' => $this->repository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']),
        ]);
    }

    /**
     * Crée un nouveau membre du personnel.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     * Si un compte est associé, le rôle ROLE_PERSONNEL y est automatiquement ajouté.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_personnels_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel, ['current_compte' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($personnel->getCompte() !== null) {
                $this->addRoleToCompte($personnel->getCompte(), 'ROLE_PERSONNEL');
            }

            $this->em->persist($personnel);
            $this->em->flush();

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

    /**
     * Modifie un membre du personnel existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     * Si un compte est associé, le rôle ROLE_PERSONNEL y est automatiquement ajouté.
     *
     * @param Personnel $personnel Le membre du personnel à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_personnels_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Personnel $personnel, Request $request): Response
    {
        $previousCompte = $personnel->getCompte();

        $form = $this->createForm(PersonnelType::class, $personnel, [
            'current_compte' => $previousCompte,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncCompteRole($previousCompte, $personnel->getCompte(), 'ROLE_PERSONNEL');

            $this->em->flush();

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

    /**
     * Supprime un membre du personnel.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param Personnel $personnel Le membre du personnel à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste du personnel
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_personnels_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Personnel $personnel, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-personnel-' . $personnel->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($personnel->getCompte() !== null) {
            $this->removeRoleFromCompte($personnel->getCompte(), 'ROLE_PERSONNEL');
        }

        $this->em->remove($personnel);
        $this->em->flush();

        $this->addFlash('success', 'Membre du personnel supprimé.');

        return $this->redirectToRoute('app_espace_personnel_personnels');
    }
}
