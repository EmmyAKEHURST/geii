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

/**
 * Gestion des enseignants de l'établissement.
 *
 * Permet d'afficher, créer, modifier et supprimer les enseignants.
 */
#[Route('/espace/personnel/enseignants')]
#[IsGranted('ROLE_PERSONNEL')]
final class EnseignantController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EnseignantRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Affiche la liste complète des enseignants.
     *
     * @description Cette liste est triée par nom et prénom en ordre croissant.
     *
     * @return Response La page HTML de la liste des enseignants
     */
    #[Route('', name: 'app_espace_personnel_enseignants', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/enseignants.html.twig', [
            'staff' => $this->getStaffData(),
            'teachers' => $this->repository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']),
        ]);
    }

    /**
     * Crée un nouvel enseignant.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     * Si un compte est associé, le rôle ROLE_ENSEIGNANT y est automatiquement ajouté.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
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

    /**
     * Modifie un enseignant existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     * Si un compte est associé, le rôle ROLE_ENSEIGNANT y est automatiquement ajouté.
     *
     * @param Enseignant $enseignant L'enseignant à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_enseignants_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Enseignant $enseignant, Request $request): Response
    {
        $previousCompte = $enseignant->getCompte();

        $form = $this->createForm(EnseignantType::class, $enseignant, [
            'current_compte' => $previousCompte,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncCompteRole($previousCompte, $enseignant->getCompte(), 'ROLE_ENSEIGNANT');

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

    /**
     * Supprime un enseignant.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param Enseignant $enseignant L'enseignant à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des enseignants
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_enseignants_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Enseignant $enseignant, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-enseignant-' . $enseignant->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($enseignant->getCompte() !== null) {
            $this->removeRoleFromCompte($enseignant->getCompte(), 'ROLE_ENSEIGNANT');
        }

        $this->em->remove($enseignant);
        $this->em->flush();

        $this->addFlash('success', 'Enseignant supprimé.');

        return $this->redirectToRoute('app_espace_personnel_enseignants');
    }
}
