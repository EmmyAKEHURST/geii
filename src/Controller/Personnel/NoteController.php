<?php

namespace App\Controller\Personnel;

use App\Entity\Note;
use App\Form\Personnel\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des notes et des évaluations des étudiants.
 *
 * Permet d'afficher, créer, modifier et supprimer les notes enregistrées.
 */
#[Route('/espace/personnel/notes')]
#[IsGranted('ROLE_PERSONNEL')]
final class NoteController extends AbstractController
{
    use StaffContextTrait;

    public function __construct(
        private readonly NoteRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Affiche la liste complète des notes.
     *
     * @description Cette liste est triée par identifiant en ordre décroissant (les plus récentes en premier).
     *
     * @return Response La page HTML de la liste des notes
     */
    #[Route('', name: 'app_espace_personnel_notes', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/personnel/notes.html.twig', [
            'staff' => $this->getStaffData(),
            'grades' => $this->repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * Crée et enregistre une nouvelle note.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_notes_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($note);
            $this->em->flush();

            $this->addFlash('success', 'Note enregistrée.');

            return $this->redirectToRoute('app_espace_personnel_notes');
        }

        return $this->render('espace/personnel/notes_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'note' => $note,
            'isNew' => true,
        ]);
    }

    /**
     * Modifie une note existante.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     *
     * @param Note $note La note à modifier
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_notes_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Note $note, Request $request): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Note mise à jour.');

            return $this->redirectToRoute('app_espace_personnel_notes');
        }

        return $this->render('espace/personnel/notes_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'note' => $note,
            'isNew' => false,
        ]);
    }

    /**
     * Supprime une note.
     *
     * @description Nécessite un token CSRF valide pour la validation du formulaire.
     *
     * @param Note $note La note à supprimer
     * @param Request $request La requête HTTP
     * @return Response Redirection vers la liste des notes
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_notes_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Note $note, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-note-' . $note->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $this->em->remove($note);
        $this->em->flush();

        $this->addFlash('success', 'Note supprimée.');

        return $this->redirectToRoute('app_espace_personnel_notes');
    }
}
