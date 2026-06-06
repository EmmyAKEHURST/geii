<?php

namespace App\Controller\Enseignant;

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
 * Gestion des notes depuis l'espace enseignant.
 *
 * Permet d'afficher, créer, modifier et supprimer les notes des étudiants.
 * Accès réservé à ROLE_ENSEIGNANT.
 */
#[Route('/espace/enseignant/notes')]
#[IsGranted('ROLE_ENSEIGNANT')]
final class NoteController extends AbstractController
{
    use TeacherContextTrait;

    public function __construct(
        private readonly NoteRepository $repository,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Affiche la liste complète des notes, triée par identifiant décroissant.
     */
    #[Route('', name: 'app_espace_enseignant_notes', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('espace/enseignant/notes.html.twig', [
            'teacher' => $this->getTeacherData(),
            'grades'  => $this->repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    /**
     * Crée et enregistre une nouvelle note.
     *
     * Affiche le formulaire en GET et traite la soumission en POST.
     */
    #[Route('/new', name: 'app_espace_enseignant_notes_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($note);
            $this->em->flush();

            $this->addFlash('success', 'Note enregistrée.');

            return $this->redirectToRoute('app_espace_enseignant_notes');
        }

        return $this->render('espace/enseignant/notes_form.html.twig', [
            'teacher' => $this->getTeacherData(),
            'form'    => $form,
            'note'    => $note,
            'isNew'   => true,
        ]);
    }

    /**
     * Modifie une note existante.
     *
     * Affiche le formulaire en GET et traite la soumission en POST.
     */
    #[Route('/{id}/edit', name: 'app_espace_enseignant_notes_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Note $note, Request $request): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Note mise à jour.');

            return $this->redirectToRoute('app_espace_enseignant_notes');
        }

        return $this->render('espace/enseignant/notes_form.html.twig', [
            'teacher' => $this->getTeacherData(),
            'form'    => $form,
            'note'    => $note,
            'isNew'   => false,
        ]);
    }

    /**
     * Supprime une note.
     *
     * Nécessite un token CSRF valide.
     */
    #[Route('/{id}/delete', name: 'app_espace_enseignant_notes_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
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

        return $this->redirectToRoute('app_espace_enseignant_notes');
    }
}
