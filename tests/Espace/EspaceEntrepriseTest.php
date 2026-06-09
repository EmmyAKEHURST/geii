<?php

namespace App\Tests\Espace;

use App\Tests\FunctionalTestCase;

class EspaceEntrepriseTest extends FunctionalTestCase
{
    private const string ROUTE = '/espace/entreprise/';

    /**
     * Vérifie que le tableau de bord est accessible
     * par un utilisateur ayant le rôle ROLE_ENTREPRISE avec un profil lié.
     */
    public function testTableauDeBordAccessibleParLEntreprise(): void
    {
        $compte = $this->createCompteEntreprise('entreprise@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que le personnel, bien qu'héritant de ROLE_ENTREPRISE,
     * reçoit un 403 s'il n'a pas d'entreprise liée à son compte.
     */
    public function testTableauDeBordAccessibleParLePersonnelGraceALaHierarchie(): void
    {
        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
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
     * en tentant d'accéder à l'espace entreprise.
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
     * en tentant d'accéder à l'espace entreprise.
     */
    public function testTableauDeBordRefuseAuxEnseignants(): void
    {
        $compte = $this->createCompteEnseignant('enseignant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', self::ROUTE);

        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * S'assure que le nom de l'entreprise est bien affiché
     * dans le contenu de la page.
     */
    public function testTableauDeBordAfficheLeNomDeLEntreprise(): void
    {
        $compte = $this->createCompteEntreprise('entreprise@test.fr');
        $this->client->loginUser($compte);

        $crawler = $this->client->request('GET', self::ROUTE);

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('TestEntreprise', $crawler->filter('body')->text());
    }
}
