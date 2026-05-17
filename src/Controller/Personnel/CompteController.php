<?php

namespace App\Controller\Personnel;

use App\Entity\Compte;
use App\Form\Personnel\CompteType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * CRUD des comptes utilisateurs (exclusif Personnel — CDC §2.2).
 *
 * Le mot de passe est hashé via UserPasswordHasherInterface ; en édition,
 * il n'est mis à jour que si l'opérateur en a saisi un nouveau.
 *
 * Garde-fou : un Personnel connecté ne peut pas supprimer son propre compte.
 */
#[Route('/espace/personnel/comptes')]
#[IsGranted('ROLE_PERSONNEL')]
final class CompteController extends AbstractController
{
    use StaffContextTrait;

    #[Route('', name: 'app_espace_personnel_comptes', methods: ['GET'])]
    public function index(CompteRepository $repository): Response
    {
        return $this->render('espace/personnel/comptes.html.twig', [
            'staff' => $this->getStaffData(),
            'accounts' => $repository->findBy([], ['email' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_espace_personnel_comptes_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ): Response {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $compte->setPassword($hasher->hashPassword($compte, (string) $form->get('plainPassword')->getData()));
            $em->persist($compte);
            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès.');

            return $this->redirectToRoute('app_espace_personnel_comptes');
        }

        return $this->render('espace/personnel/comptes_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'compte' => $compte,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_espace_personnel_comptes_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        Compte $compte,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
    ): Response {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = (string) $form->get('plainPassword')->getData();
            if ($plain !== '') {
                $compte->setPassword($hasher->hashPassword($compte, $plain));
            }
            $em->flush();

            $this->addFlash('success', 'Compte mis à jour.');

            return $this->redirectToRoute('app_espace_personnel_comptes');
        }

        return $this->render('espace/personnel/comptes_form.html.twig', [
            'staff' => $this->getStaffData(),
            'form' => $form,
            'compte' => $compte,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_espace_personnel_comptes_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Compte $compte, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-compte-' . $compte->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($compte === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');

            return $this->redirectToRoute('app_espace_personnel_comptes');
        }

        $em->remove($compte);
        $em->flush();

        $this->addFlash('success', 'Compte supprimé.');

        return $this->redirectToRoute('app_espace_personnel_comptes');
    }
}
