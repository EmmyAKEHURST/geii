<?php

namespace App\Tests\Espace;

use App\Tests\FunctionalTestCase;

class EspaceEtudiantTest extends FunctionalTestCase
{
    /**
     * Vérifie que le tableau de bord est accessible
     * par un utilisateur ayant le rôle ROLE_ETUDIANT.
     */
    public function testTableauDeBordAccessibleParLEtudiant(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie qu'un visiteur non authentifié est redirigé
     * hors de l'espace étudiant.
     */
    public function testTableauDeBordRefuseAuxInvites(): void
    {
        $this->client->request('GET', '/espace/etudiant/');

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * Vérifie que la page des notes est accessible
     * pour un étudiant connecté.
     */
    public function testPageNotesAccessible(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/notes');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que la page de l'emploi du temps est accessible
     * pour un étudiant connecté.
     */
    public function testPageEmploiDuTempsAccessible(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/emploi-du-temps');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que la page des offres d'alternance est accessible
     * pour un étudiant connecté.
     */
    public function testPageOffresAlternanceAccessible(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/offres-alternance');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que la page des projets tuteurés est accessible
     * pour un étudiant connecté.
     */
    public function testPageProjetsTuteuresAccessible(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/projets-tuteures');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que la page des supports de cours est accessible
     * pour un étudiant connecté.
     */
    public function testPageSupportsCours(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/supports-cours');

        $this->assertResponseIsSuccessful();
    }
}
