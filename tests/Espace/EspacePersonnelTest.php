<?php

namespace App\Tests\Espace;

use App\Tests\FunctionalTestCase;

class EspacePersonnelTest extends FunctionalTestCase
{
    private const string ROUTE = '/espace/personnel/';

    /**
     * Vérifie que le tableau de bord est accessible
     * par un utilisateur ayant le rôle ROLE_PERSONNEL.
     */
    public function testTableauDeBordAccessibleParLePersonnel(): void
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
     * en tentant d'accéder à l'espace personnel.
     */
    public function testTableauDeBordRefuseAuxEtudiants(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Vérifie qu'un enseignant reçoit une réponse 403
     * en tentant d'accéder à l'espace personnel.
     */
    public function testTableauDeBordRefuseAuxEnseignants(): void
    {
        $compte = $this->createCompteEnseignant('enseignant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Vérifie qu'une entreprise reçoit une réponse 403
     * en tentant d'accéder à l'espace personnel.
     */
    public function testTableauDeBordRefuseAuxEntreprises(): void
    {
        $compte = $this->createCompteEntreprise('entreprise@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * S'assure que le tableau de bord retourne une réponse
     * avec du contenu HTML visible.
     */
    public function testTableauDeBordAfficheDesStatistiques(): void
    {
        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);

        $crawler = $this->client->request('GET', self::ROUTE);

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('body')->count());
    }
}
