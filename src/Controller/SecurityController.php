<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Manages authentication-related pages such as login and password-reset request.
 */
class SecurityController extends AbstractController
{
    /**
     * Displays the login page with the last entered username and the latest authentication error.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Displays the password reset request form and handles the request submission.
     */
    #[Route(path: '/mot-de-passe-oublie', name: 'app_forgot_password_request', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $csrfToken = (string) $request->request->get('_csrf_token');
            if (!$this->isCsrfTokenValid('forgot_password', $csrfToken)) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }

            $this->addFlash('success', 'Si cette adresse existe, un email de réinitialisation a été envoyé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/forgot_password.html.twig');
    }

    /**
     * Route endpoint intercepted by Symfony's security system to log out the current user.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Automatically logout the user.
    }
}
