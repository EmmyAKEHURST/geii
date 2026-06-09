<?php

namespace App\Tests\Espace;

use App\Tests\FunctionalTestCase;

/**
 * Vérifie les redirections de /espaces/attente-validation selon le rôle de l'utilisateur.
 */
class EspaceAttenteValidationTest extends FunctionalTestCase
{
    private const string ROUTE = '/espaces/attente-validation';

    /**
     * Vérifie qu'un visiteur non authentifié est redirigé
     * vers la page de connexion.
     */
    public function testPageRequiresAuthentication(): void
    {
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseRedirects('/login');
    }

    /**
     * Vérifie qu'un utilisateur sans rôle spécifique
     * voit la page d'attente de validation.
     */
    public function testRoleUserVoitPageAttenteValidation(): void
    {
        $compte = $this->createCompte('user@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que le personnel est automatiquement redirigé
     * vers son espace dédié.
     */
    public function testRolePersonnelEstRedirigeVersEspacePersonnel(): void
    {
        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseRedirects('/espace/personnel/');
    }

    /**
     * Vérifie que l'étudiant est automatiquement redirigé
     * vers son espace dédié.
     */
    public function testRoleEtudiantEstRedirigeVersEspaceEtudiant(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseRedirects('/espace/etudiant/');
    }

    /**
     * Vérifie que l'entreprise avec un profil lié
     * est redirigée vers son espace dédié.
     */
    public function testRoleEntrepriseAvecEntrepriseEstRedirigeVersEspaceEntreprise(): void
    {
        $compte = $this->createCompteEntreprise('entreprise@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', self::ROUTE);

        $this->assertResponseRedirects('/espace/entreprise/');
    }
}
