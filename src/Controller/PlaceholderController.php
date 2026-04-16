<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Routes référencées par le template d'accueil
 */
final class PlaceholderController extends AbstractController
{
    /**
     * Renders the public GEII presentation page.
     */
    #[Route('/presentation', name: 'app_presentation')]
    public function presentation(): Response
    {
        return $this->render('pages/presentation/geii.html.twig');
    }

    /**
     * Renders the LP MIAW public page.
     */
    #[Route('/licences/miaw', name: 'app_lp_miaw')]
    public function lpMiaw(): Response
    {
        return $this->render('pages/lp/miaw.html.twig');
    }

    /**
     * Renders the LP ABDD public page.
     */
    #[Route('/licences/abdd', name: 'app_lp_abdd')]
    public function lpAbdd(): Response
    {
        return $this->render('pages/lp/abdd.html.twig');
    }

    /**
     * Renders the LP GTHBT public page.
     */
    #[Route('/licences/gthbt', name: 'app_lp_gthbt')]
    public function lpGthbt(): Response
    {
        return $this->render('pages/lp/gthbt.html.twig');
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
