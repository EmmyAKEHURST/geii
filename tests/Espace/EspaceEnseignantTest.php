<?php

namespace App\Tests\Espace;

use App\Tests\FunctionalTestCase;

class EspaceEnseignantTest extends FunctionalTestCase
{
    private const string ROUTE = '/espace/enseignant/';

    /**
     * Vérifie que le tableau de bord est accessible
     * par un utilisateur ayant le rôle ROLE_ENSEIGNANT.
     */
    public function testTableauDeBordAccessibleParLEnseignant(): void
    {
        $compte = $this->createCompteEnseignant('enseignant@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', self::ROUTE);

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que le personnel peut accéder à l'espace enseignant
     * grâce à la hiérarchie des rôles (ROLE_PERSONNEL hérite de ROLE_ENSEIGNANT).
     */
    public function testTableauDeBordAccessibleParLePersonnelGraceALaHierarchie(): void
    {
        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', self::ROUTE);

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie qu'un visiteur non authentifié est redirigé
     * vers la page de connexion.
     */
    public function testTableauDeBordRefuseAuxInvites(): void
    {
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseRedirects('/login');
    }

    /**
     * Vérifie qu'un étudiant reçoit une réponse 403
     * en tentant d'accéder à l'espace enseignant.
     */
    public function testTableauDeBordRefuseAuxEtudiants(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Vérifie qu'une entreprise reçoit une réponse 403
     * en tentant d'accéder à l'espace enseignant.
     */
    public function testTableauDeBordRefuseAuxEntreprises(): void
    {
        $compte = $this->createCompteEntreprise('entreprise@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
    }
}
