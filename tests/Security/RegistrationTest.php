<?php

namespace App\Tests\Security;

use App\Entity\Compte;
use App\Tests\FunctionalTestCase;

class RegistrationTest extends FunctionalTestCase
{
    /**
     * Vérifie que la page d'inscription répond avec un statut 200.
     */
    public function testPageInscriptionEstAccessible(): void
    {
        $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que la page d'inscription contient bien un formulaire HTML.
     */
    public function testPageInscriptionContientUnFormulaire(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('form'));
    }

    /**
     * Vérifie que le formulaire expose les champs email, accord,
     * et les deux champs de saisie du mot de passe.
     */
    public function testPageInscriptionContientLesChamps(): void
    {
        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThanOrEqual(1, $crawler->filter('input[name="registration_form[email]"]')->count());
        $this->assertCount(1, $crawler->filter('input[name="registration_form[agreeTerms]"]'));
        $this->assertCount(1, $crawler->filter('input[name="registration_form[plainPassword][first]"]'));
        $this->assertCount(1, $crawler->filter('input[name="registration_form[plainPassword][second]"]'));
    }

    /**
     * Vérifie qu'une soumission sans adresse email
     * retourne une erreur de validation (422).
     */
    public function testSoumettreFormulaireSansEmailAfficheErreur(): void
    {
        $this->client->request('GET', '/register');
        $this->client->submitForm('Créer mon compte', [
            'registration_form[email]' => '',
            'registration_form[plainPassword][first]' => 'TestPassword1!',
            'registration_form[plainPassword][second]' => 'TestPassword1!',
            'registration_form[agreeTerms]' => '1',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * Vérifie qu'une soumission sans cocher les CGU
     * retourne une erreur de validation (422).
     */
    public function testSoumettreFormulaireSansAccordAfficheErreur(): void
    {
        $this->client->request('GET', '/register');
        $this->client->submitForm('Créer mon compte', [
            'registration_form[email]' => 'nouveau@test.fr',
            'registration_form[plainPassword][first]' => 'TestPassword1!',
            'registration_form[plainPassword][second]' => 'TestPassword1!',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * Vérifie que la saisie de deux mots de passe différents
     * déclenche une erreur de validation (422).
     */
    public function testSoumettreFormulaireAvecMotsDePasseInegauxAfficheErreur(): void
    {
        $this->client->request('GET', '/register');
        $this->client->submitForm('Créer mon compte', [
            'registration_form[email]' => 'nouveau@test.fr',
            'registration_form[plainPassword][first]' => 'TestPassword1!',
            'registration_form[plainPassword][second]' => 'AutreMotDePasse2!extra',
            'registration_form[agreeTerms]' => '1',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    /**
     * S'assure qu'un compte est bien persisté en base de données
     * après une inscription avec des données valides.
     */
    public function testSoumettreFormulaireValideCreeLECompte(): void
    {
        $this->client->request('GET', '/register');
        $this->client->submitForm('Créer mon compte', [
            'registration_form[email]' => 'nouveau@test.fr',
            'registration_form[plainPassword][first]' => 'TestPassword1!',
            'registration_form[plainPassword][second]' => 'TestPassword1!',
            'registration_form[agreeTerms]' => '1',
        ]);

        $this->em->clear();
        $compte = $this->em->getRepository(Compte::class)->findOneBy(['email' => 'nouveau@test.fr']);
        $this->assertNotNull($compte);
    }

    /**
     * Vérifie qu'une inscription avec un email déjà utilisé
     * retourne une erreur de validation (422).
     */
    public function testSoumettreFormulaireAvecEmailDejaUtiliseAfficheErreur(): void
    {
        $this->createCompte('existant@test.fr');

        $this->client->request('GET', '/register');
        $this->client->submitForm('Créer mon compte', [
            'registration_form[email]' => 'existant@test.fr',
            'registration_form[plainPassword][first]' => 'TestPassword1!',
            'registration_form[plainPassword][second]' => 'TestPassword1!',
            'registration_form[agreeTerms]' => '1',
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
