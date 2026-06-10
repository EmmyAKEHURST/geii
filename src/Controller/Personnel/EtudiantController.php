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

    /**
     * Affiche la liste complète des étudiants.
     *
     * @description Cette liste est triée par nom et prénom en ordre croissant.
     *
     * @return Response La page HTML de la liste des étudiants
     */
    #[Route('', name: 'app_espace_personnel_etudiants', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/etudiants.html.twig', [
            'staff' => $this->getStaffData(),
            'students' => $this->repository->findBy([], ['nom' => 'ASC', 'prenom' => 'ASC']),
        ]);
    }

    /**
     * Crée un nouvel étudiant.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     * Si un compte est associé, le rôle ROLE_ETUDIANT y est automatiquement ajouté.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
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

    /**
     * Modifie un étudiant existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     * Si un compte est associé, le rôle ROLE_ETUDIANT y est automatiquement ajouté.
     *
     * @param Etudiant $etudiant L'étudiant à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{numEtudiant}/edit', name: 'app_espace_personnel_etudiants_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity(mapping: ['numEtudiant' => 'num_etudiant'])] Etudiant $etudiant,
        Request $request,
    ): Response {
        $previousCompte = $etudiant->getCompte();

        $form = $this->createForm(EtudiantType::class, $etudiant, [
            'current_compte' => $previousCompte,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncCompteRole($previousCompte, $etudiant->getCompte(), 'ROLE_ETUDIANT');

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

    /**
     * Supprime un étudiant.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param Etudiant $etudiant L'étudiant à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des étudiants
     */
    #[Route('/{numEtudiant}/delete', name: 'app_espace_personnel_etudiants_delete', methods: ['POST'])]
    public function delete(
        #[MapEntity(mapping: ['numEtudiant' => 'num_etudiant'])] Etudiant $etudiant, Request $request
    ): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-etudiant-' . $etudiant->getNumEtudiant(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($etudiant->getCompte() !== null) {
            $this->removeRoleFromCompte($etudiant->getCompte(), 'ROLE_ETUDIANT');
        }

        $this->em->remove($etudiant);
        $this->em->flush();

        $this->addFlash('success', 'Étudiant supprimé.');

        return $this->redirectToRoute('app_espace_personnel_etudiants');
    }
}
