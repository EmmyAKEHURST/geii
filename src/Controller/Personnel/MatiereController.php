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

/**
 * Gestion des matières/disciplines enseignées.
 *
 * Permet d'afficher, créer, modifier et supprimer les matières de l'établissement.
 */
#[Route('/espace/personnel/matieres')]
#[IsGranted('ROLE_PERSONNEL')]
final class MatiereController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly MatiereRepository $repository,
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Affiche la liste complète des matières.
     *
     * @description Cette liste est triée par nom en ordre croissant.
     *
     * @return Response La page HTML de la liste des matières
     */
    #[Route('', name: 'app_espace_personnel_matieres', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/matieres.html.twig', [
            'staff' => $this->getStaffData(),
            'subjects' => $this->repository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    /**
     * Crée une nouvelle matière.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_matieres_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($matiere);
            $this->em->flush();

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

    /**
     * Modifie une matière existante.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     *
     * @param Matiere $matiere La matière à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_matieres_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Matiere $matiere, Request $request): Response
    {
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

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

    /**
     * Supprime une matière.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param Matiere $matiere La matière à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des matières
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_matieres_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Matiere $matiere, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-matiere-' . $matiere->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($matiere);
        $this->em->flush();

        $this->addFlash('success', 'Matière supprimée.');

        return $this->redirectToRoute('app_espace_personnel_matieres');
    }
}
