<?php

namespace App\Controller;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Point d'entrée « Mon compte » pour les utilisateurs sans espace dédié encore activé.
 */
final class EspaceController extends AbstractController
{
    #[Route('/espaces/attente-validation', name: 'app_espaces_attente_validation')]
    #[IsGranted('ROLE_USER')]
    public function attenteValidation(): Response
    {
        if ($this->isGranted('ROLE_PERSONNEL')) {
            return $this->redirectToRoute('app_espace_personnel_index');
        }

        /** @var Compte $user */
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ENTREPRISE') && $user->getEntreprise() !== null) {
            return $this->redirectToRoute('app_espace_entreprise_index');
        }

        if ($this->isGranted('ROLE_ETUDIANT')) {
            return $this->redirectToRoute('app_espace_etudiant_index');
        }

        return $this->render('espace/en-attente.html.twig');
    }
}
