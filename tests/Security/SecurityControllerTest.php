<?php

namespace App\Tests\Security;

use App\Tests\FunctionalTestCase;

class SecurityControllerTest extends FunctionalTestCase
{
    /**
     * Vérifie que la page de connexion répond avec un statut 200.
     */
    public function testLoginPageIsAccessible(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que le formulaire de connexion contient les champs email,
     * mot de passe et jeton CSRF.
     */
    public function testLoginPageHasEmailAndPasswordFields(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('input[name="_username"]'));
        $this->assertCount(1, $crawler->filter('input[name="_password"]'));
        $this->assertCount(1, $crawler->filter('input[name="_csrf_token"]'));
    }

    /**
     * Vérifie qu'une connexion avec des identifiants valides
     * déclenche une redirection.
     */
    public function testLoginWithValidCredentialsRedirects(): void
    {
        $this->createCompte('user@test.fr', [], 'Password1!');

        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', [
            '_username' => 'user@test.fr',
            '_password' => 'Password1!',
        ]);

        $this->assertResponseRedirects();
    }

    /**
     * Vérifie qu'une connexion avec des identifiants incorrects
     * redirige vers la page de connexion.
     */
    public function testLoginWithInvalidCredentialsRedirectsBackToLogin(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', [
            '_username' => 'inexistant@test.fr',
            '_password' => 'mauvais_mot_de_passe',
        ]);

        $this->assertResponseRedirects('/login');
    }

    /**
     * S'assure qu'un message d'erreur est affiché après
     * une tentative de connexion échouée.
     */
    public function testLoginWithInvalidCredentialsShowsError(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', [
            '_username' => 'inexistant@test.fr',
            '_password' => 'mauvais_mot_de_passe',
        ]);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    /**
     * Vérifie que la page "Mot de passe oublié" répond avec un statut 200.
     */
    public function testForgotPasswordPageIsAccessible(): void
    {
        $this->client->request('GET', '/mot-de-passe-oublie');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que le formulaire de réinitialisation contient
     * un champ email et un jeton CSRF.
     */
    public function testForgotPasswordPageHasEmailField(): void
    {
        $crawler = $this->client->request('GET', '/mot-de-passe-oublie');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('input[name="email"]'));
        $this->assertCount(1, $crawler->filter('input[name="_csrf_token"]'));
    }

    /**
     * Vérifie que la soumission du formulaire redirige vers la page de connexion.
     */
    public function testForgotPasswordSubmitRedirectsToLogin(): void
    {
        $this->client->request('GET', '/mot-de-passe-oublie');
        $this->client->submitForm('Envoyer le lien de réinitialisation', [
            'email' => 'utilisateur@test.fr',
        ]);

        $this->assertResponseRedirects('/login');
    }

    /**
     * S'assure qu'un message flash de confirmation est affiché
     * après la soumission du formulaire de réinitialisation.
     */
    public function testForgotPasswordSubmitAddsFlashMessage(): void
    {
        $this->client->request('GET', '/mot-de-passe-oublie');
        $this->client->submitForm('Envoyer le lien de réinitialisation', [
            'email' => 'utilisateur@test.fr',
        ]);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-success');
    }

    /**
     * Vérifie que la déconnexion d'un utilisateur authentifié
     * déclenche une redirection.
     */
    public function testLogoutWhileAuthenticatedRedirects(): void
    {
        $compte = $this->createCompte('logout@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }
}
