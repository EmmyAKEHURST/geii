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

#[Route('/espace/personnel/notes')]
#[IsGranted('ROLE_PERSONNEL')]
final class NoteController extends AbstractController
{
    use StaffContextTrait;

    #[Route('', name: 'app_espace_personnel_notes', methods: ['GET'])]
    public function index(NoteRepository $repository): Response
    {
        return $this->render('espace/personnel/notes.html.twig', [
            'staff' => $this->getStaffData(),
            'grades' => $repository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_notes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($note);
            $em->flush();
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

    #[Route('/{id}/edit', name: 'app_espace_personnel_notes_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Note $note, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
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

    #[Route('/{id}/delete', name: 'app_espace_personnel_notes_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Note $note, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-note-' . $note->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $em->remove($note);
        $em->flush();
        $this->addFlash('success', 'Note supprimée.');

        return $this->redirectToRoute('app_espace_personnel_notes');
    }
}
