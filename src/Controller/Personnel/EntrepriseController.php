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

/**
 * Gestion des entreprises partenaires.
 *
 * Permet d'afficher, créer, modifier et supprimer les entreprises.
 */
#[Route('/espace/personnel/entreprises')]
#[IsGranted('ROLE_PERSONNEL')]
final class EntrepriseController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EntrepriseRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Affiche la liste complète des entreprises.
     *
     * @description Cette liste est triée par nom en ordre croissant.
     *
     * @return Response La page HTML de la liste des entreprises
     */
    #[Route('', name: 'app_espace_personnel_entreprises', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/entreprises.html.twig', [
            'staff' => $this->getStaffData(),
            'companies' => $this->repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    /**
     * Crée une nouvelle entreprise.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_entreprises_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise, ['current_compte' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entreprise->getCompte() !== null) {
                $this->addRoleToCompte($entreprise->getCompte(), 'ROLE_ENTREPRISE');
            }

            $this->em->persist($entreprise);
            $this->em->flush();

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

    /**
     * Modifie une entreprise existante.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     *
     * @param Entreprise $entreprise L'entreprise à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_entreprises_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Entreprise $entreprise, Request $request): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise, [
            'current_compte' => $entreprise->getCompte(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($entreprise->getCompte() !== null) {
                $this->addRoleToCompte($entreprise->getCompte(), 'ROLE_ENTREPRISE');
            }

            $this->em->flush();

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

    /**
     * Supprime une entreprise.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param Entreprise $entreprise L'entreprise à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des entreprises
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_entreprises_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Entreprise $entreprise, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-entreprise-' . $entreprise->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($entreprise);
        $this->em->flush();

        $this->addFlash('success', 'Entreprise supprimée.');

        return $this->redirectToRoute('app_espace_personnel_entreprises');
    }
}
