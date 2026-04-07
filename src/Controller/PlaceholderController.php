<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Routes référencées par le template d'accueil
 */
final class PlaceholderController extends AbstractController
{
    #[Route('/presentation', name: 'app_presentation')]
    public function presentation(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/licences/miaw', name: 'app_lp_miaw')]
    public function lpMiaw(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/licences/abdd', name: 'app_lp_abdd')]
    public function lpAbdd(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/licences/gthbt', name: 'app_lp_gthbt')]
    public function lpGthbt(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/espace-etudiant', name: 'app_espace_etudiant')]
    public function espaceEtudiant(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/espace-enseignant', name: 'app_espace_enseignant')]
    public function espaceEnseignant(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/login', name: 'app_login')]
    public function login(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/alternance/offre', name: 'app_alternance_offre')]
    public function alternanceOffre(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/projet-tuteure', name: 'app_projet_tuteure')]
    public function projetTuteure(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }
}
