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
    /**
     * Redirects the presentation placeholder route to the homepage.
     */
    #[Route('/presentation', name: 'app_presentation')]
    public function presentation(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the LP MIAW placeholder route to the homepage.
     */
    #[Route('/licences/miaw', name: 'app_lp_miaw')]
    public function lpMiaw(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the LP ABDD placeholder route to the homepage.
     */
    #[Route('/licences/abdd', name: 'app_lp_abdd')]
    public function lpAbdd(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the LP GTHBT placeholder route to the homepage.
     */
    #[Route('/licences/gthbt', name: 'app_lp_gthbt')]
    public function lpGthbt(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the student space placeholder route to the homepage.
     */
    #[Route('/espace-etudiant', name: 'app_espace_etudiant')]
    public function espaceEtudiant(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the teacher space placeholder route to the homepage.
     */
    #[Route('/espace-enseignant', name: 'app_espace_enseignant')]
    public function espaceEnseignant(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the legacy login placeholder route to the homepage.
     */
    #[Route('/login', name: 'app_login')]
    public function login(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the work-study offer placeholder route to the homepage.
     */
    #[Route('/alternance/offre', name: 'app_alternance_offre')]
    public function alternanceOffre(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Redirects the supervised project placeholder route to the homepage.
     */
    #[Route('/projet-tuteure', name: 'app_projet_tuteure')]
    public function projetTuteure(): RedirectResponse
    {
        return $this->redirectToRoute('app_home');
    }
}
