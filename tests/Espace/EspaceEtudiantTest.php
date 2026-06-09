<?php

namespace App\Tests\Espace;

use App\Entity\EmploiDuTemps;
use App\Entity\Entreprise;
use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Note;
use App\Entity\OffreAlternance;
use App\Enum\StatutAlternance;
use App\Tests\FunctionalTestCase;
use DateTime;

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

    // ── EDT avec navigation par semaine ─────────────────────────────────────

    /**
     * Vérifie que l'EDT est accessible avec le paramètre week=1 (semaine suivante).
     */
    public function testPageEmploiDuTempsAvecSemaineSuivante(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/emploi-du-temps?week=1');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que l'EDT est accessible avec le paramètre week=-1 (semaine précédente).
     */
    public function testPageEmploiDuTempsAvecSemainePrecedente(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $this->client->request('GET', '/espace/etudiant/emploi-du-temps?week=-1');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie que les boutons de navigation (précédente/suivante) sont présents dans l'EDT.
     */
    public function testPageEmploiDuTempsAfficheNavigationSemaine(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $crawler = $this->client->request('GET', '/espace/etudiant/emploi-du-temps');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThanOrEqual(2, $crawler->filter('a[href*="week="]')->count());
    }

    /**
     * Vérifie que le bouton "Aujourd'hui" apparaît quand on consulte une autre semaine.
     */
    public function testPageEmploiDuTempsAfficheBoutonAujourdhuiSurAutreSemaine(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $crawler = $this->client->request('GET', '/espace/etudiant/emploi-du-temps?week=2');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("Aujourd'hui", $crawler->filter('body')->text());
    }

    // ── Notes avec commentaire ───────────────────────────────────────────────

    /**
     * Vérifie que la colonne "Commentaire" est présente dans le tableau des notes.
     */
    public function testPageNotesAfficheColonneCommentaire(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $crawler = $this->client->request('GET', '/espace/etudiant/notes');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Commentaire', $crawler->filter('table thead')->text());
    }

    /**
     * Vérifie que le commentaire d'une note est affiché dans le tableau.
     */
    public function testPageNotesAfficheCommentaireDeLaNote(): void
    {
        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $etudiant = $this->em->getRepository(Etudiant::class)->findOneBy(['compte' => $compte]);

        $matiere = (new Matiere())->setNom('Électronique');
        $this->em->persist($matiere);

        $note = (new Note())
            ->setValeur(15.0)
            ->setMatiere($matiere)
            ->setEtudiant($etudiant)
            ->setCommentaire('Très bon travail')
        ;
        $this->em->persist($note);
        $this->em->flush();

        $this->client->loginUser($compte);
        $crawler = $this->client->request('GET', '/espace/etudiant/notes');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Très bon travail', $crawler->filter('table tbody')->text());
    }

    // ── Alternance avec statut ───────────────────────────────────────────────

    /**
     * Vérifie que le statut d'une offre d'alternance est affiché dans la page.
     */
    public function testPageOffresAlternanceAfficheStatutOffre(): void
    {
        $entreprise = (new Entreprise())
            ->setNom('TechCorp')
            ->setSiret('99887766554433')
            ->setAdresse('1 avenue des Tests')
            ->setSecteur('Informatique')
        ;
        $this->em->persist($entreprise);

        $offre = (new OffreAlternance())
            ->setTitre('Développeur Symfony')
            ->setDescription('Alternance Symfony.')
            ->setDuree(12)
            ->setStatut(StatutAlternance::ACTIVE)
            ->setEntreprise($entreprise)
        ;
        $this->em->persist($offre);
        $this->em->flush();

        $compte = $this->createCompteEtudiant('etudiant@test.fr');
        $this->client->loginUser($compte);
        $crawler = $this->client->request('GET', '/espace/etudiant/offres-alternance');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Active', $crawler->filter('body')->text());
    }
}
