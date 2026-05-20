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

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    /**
     * Affiche la liste complète des comptes utilisateurs.
     *
     * @description Cette liste est triée par adresse e-mail en ordre croissant.
     *
     * @param CompteRepository $repository Repository pour accéder aux comptes
     * @return Response La page HTML de la liste des comptes
     */
    #[Route('', name: 'app_espace_personnel_comptes', methods: ['GET'])]
    public function index(CompteRepository $repository): Response
    {
        return $this->render('espace/personnel/comptes.html.twig', [
            'staff' => $this->getStaffData(),
            'accounts' => $repository->findBy([], ['email' => 'ASC']),
        ]);
    }

    /**
     * Crée un nouveau compte utilisateur.
     *
     * @description Affiche le formulaire de création en GET et traite la soumission en POST.
     * Le mot de passe est hashé via `UserPasswordHasherInterface`.
     *
     * @param Request $request La requête HTTP
     * @return Response La page HTML du formulaire ou redirection après création
     */
    #[Route('/new', name: 'app_espace_personnel_comptes_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $compte = new Compte();

        $form = $this->createForm(CompteType::class, $compte, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plain */
            $plain = $form->get('plainPassword')->getData();

            $compte->setPassword($this->hasher->hashPassword($compte, $plain));

            $this->em->persist($compte);
            $this->em->flush();

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

    /**
     * Modifie un compte utilisateur existant.
     *
     * @description Affiche le formulaire d'édition en GET et traite la soumission en POST.
     * Le mot de passe n'est mis à jour que s'il est modifié. Un Personnel connecté
     * ne peut pas modifier ses propres rôles.
     *
     * @param Compte $compte Le compte à modifier
     * @param Request $request La requête HTTP
     *
     * @return Response La page HTML du formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: 'app_espace_personnel_comptes_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Compte $compte, Request $request): Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plain */
            $plain = $form->get('plainPassword')->getData();

            if ($plain !== '') {
                $compte->setPassword($this->hasher->hashPassword($compte, $plain));
            }

            $this->em->flush();

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

    /**
     * Supprime un compte utilisateur.
     *
     * @description Nécessite un token CSRF valide. Un Personnel connecté ne peut pas
     * supprimer son propre compte.
     *
     * @param Compte $compte Le compte à supprimer
     * @param Request $request La requête HTTP
     *
     * @return Response Redirection vers la liste des comptes
     */
    #[Route('/{id}/delete', name: 'app_espace_personnel_comptes_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Compte $compte, Request $request): Response
    {
        /** @var string $csrfToken */
        $csrfToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-compte-' . $compte->getId(), $csrfToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        if ($compte === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');

            return $this->redirectToRoute('app_espace_personnel_comptes');
        }

        $this->em->remove($compte);
        $this->em->flush();

        $this->addFlash('success', 'Compte supprimé.');

        return $this->redirectToRoute('app_espace_personnel_comptes');
    }
}
