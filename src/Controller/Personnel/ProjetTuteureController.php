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

/**
 * Gestion des projets tuteurés des étudiants.
 *
 * Permet d'afficher, créer, modifier et supprimer les projets tuteurés.
 */
#[Route('/espace/personnel/projets-tuteures')]
#[IsGranted('ROLE_PERSONNEL')]
final class ProjetTuteureController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ProjetTuteureRepository $repository
    ) {}

    /**
     * Affiche la liste complète des projets tuteurés.
     *
     * @description Cette liste est triée par année en ordre décroissant, puis par titre en ordre croissant.
     *
     * @return Response La page HTML de la liste des projets
     */
    #[Route('', name: 'app_espace_personnel_projets_tuteures', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/projets-tuteures.html.twig', [
            'staff' => $this->getStaffData(),
            'projects' => $this->repository->findBy([], ['annee' => 'DESC', 'titre' => 'ASC']),
        ]);
    }

    /**
     * Crée un nouveau projet tuteuré.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_projets_tuteures_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $project = new ProjetTuteure();
        $form = $this->createForm(ProjetTuteureType::class, $project, ['is_personnel' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($project);
            $this->em->flush();

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

    /**
     * Modifie un projet tuteuré existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     *
     * @param ProjetTuteure $project Le projet à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_projets_tuteures_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(ProjetTuteure $project, Request $request): Response
    {
        $form = $this->createForm(ProjetTuteureType::class, $project, ['is_personnel' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

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

    /**
     * Supprime un projet tuteuré.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param ProjetTuteure $project Le projet à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des projets
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_projets_tuteures_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(ProjetTuteure $project, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-projet-' . $project->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($project);
        $this->em->flush();

        $this->addFlash('success', 'Projet supprimé.');

        return $this->redirectToRoute('app_espace_personnel_projets_tuteures');
    }
}
