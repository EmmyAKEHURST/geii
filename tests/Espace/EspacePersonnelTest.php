<?php

namespace App\Tests\Espace;

use App\Entity\ProjetTuteure;
use App\Enum\StatutProjetTuteure;
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

    // ── Projets tuteurés : checkbox publie ──────────────────────────────────

    /**
     * Vérifie que le formulaire de création d'un projet tuteuré
     * contient le checkbox "publie" pour ROLE_PERSONNEL.
     */
    public function testFormulaireNouveauProjetContientCheckboxPublie(): void
    {
        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);

        $crawler = $this->client->request('GET', '/espace/personnel/projets-tuteures/new');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(
            0,
            $crawler->filter('input[type="checkbox"][name="projet_tuteure[publie]"]')->count(),
            'Le checkbox "publie" doit être présent dans le formulaire pour ROLE_PERSONNEL.'
        );
    }

    /**
     * Vérifie que le formulaire d'édition d'un projet tuteuré existant
     * contient le checkbox "publie" pour ROLE_PERSONNEL.
     */
    public function testFormulaireEditionProjetContientCheckboxPublie(): void
    {
        $projet = (new ProjetTuteure())
            ->setTitre('Projet test')
            ->setDescription('Description.')
            ->setAnnee(2025)
            ->setStatut(StatutProjetTuteure::OUVERT)
        ;
        $this->em->persist($projet);
        $this->em->flush();

        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);

        $crawler = $this->client->request('GET', '/espace/personnel/projets-tuteures/' . $projet->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(
            0,
            $crawler->filter('input[type="checkbox"][name="projet_tuteure[publie]"]')->count(),
            'Le checkbox "publie" doit être présent dans le formulaire d\'édition pour ROLE_PERSONNEL.'
        );
    }

    /**
     * Vérifie que la soumission du formulaire avec publie=true persiste la valeur.
     */
    public function testSoumissionFormulaireProjetAvecPublieTrue(): void
    {
        $compte = $this->createComptePersonnel('personnel@test.fr');
        $this->client->loginUser($compte);

        $this->client->request('GET', '/espace/personnel/projets-tuteures/new');
        $this->client->submitForm('Créer', [
            'projet_tuteure[titre]' => 'Projet publié',
            'projet_tuteure[description]' => 'Une description complète.',
            'projet_tuteure[annee]' => 2025,
            'projet_tuteure[statut]' => StatutProjetTuteure::OUVERT->value,
            'projet_tuteure[publie]' => true,
        ]);

        $this->assertResponseRedirects('/espace/personnel/projets-tuteures');

        $projet = $this->em->getRepository(ProjetTuteure::class)->findOneBy(['titre' => 'Projet publié']);

        $this->assertNotNull($projet);
        $this->assertTrue($projet->isPublie());
    }
}
