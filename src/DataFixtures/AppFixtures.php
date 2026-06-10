<?php

namespace App\DataFixtures;

use App\Entity\{
    Compte,
    EmploiDuTemps,
    Enseignant,
    Entreprise,
    Etudiant,
    Matiere,
    Note,
    OffreAlternance,
    Personnel,
    ProjetTuteure,
    SupportCours
};
use App\Enum\{StatutAlternance, StatutProjetTuteure};
use DateTime;
use DateMalformedStringException;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        $matieres = $this->addMatieres($manager);
        $etudiants = $this->addEtudiants($manager);
        $enseignants = $this->addEnseignants($manager);
        $entreprises = $this->addEntreprises($manager);

        $this->addEmploisDuTemps($manager, $matieres);
        $this->addNotes($manager, $matieres, $etudiants);
        $this->addPersonnel($manager);
        $this->addOffresAlternance($manager, $entreprises);
        $this->addProjetsTutores($manager, $entreprises, $enseignants);
        $this->addSupportsCours($manager);

        $manager->flush();
    }

    /**
     * Crée les matières et retourne la liste.
     *
     * @return list<Matiere>
     */
    private function addMatieres(ObjectManager $manager): array
    {
        $noms = [
            'Électronique Numérique',
            'Systèmes Embarqués',
            'Automatisme',
            'Projet Tuteuré',
            'Mathématiques Appliquées',
            'Communication Professionnelle',
            'Réseaux Industriels',
            'TP Électronique',
        ];

        $matieres = [];

        foreach ($noms as $nom) {
            $matiere = (new Matiere())->setNom($nom);
            $manager->persist($matiere);

            $matieres[] = $matiere;
        }

        return $matieres;
    }

    /**
     * Crée les créneaux d'emploi du temps de la semaine courante.
     *
     * @param list<Matiere> $matieres
     * @throws DateMalformedStringException
     */
    private function addEmploisDuTemps(ObjectManager $manager, array $matieres): void
    {
        $monday = new DateTime('monday this week');

        // [dayOffset, startHour, startMin, durationMinutes]
        $slots = [
            [0, 9, 0, 90], [0, 13, 0, 120],
            [1, 9, 0, 120], [1, 14, 0, 90],
            [2, 10, 0, 90],
            [3, 9, 0, 60], [3, 13, 0, 120],
            [4, 9, 0, 90],
        ];

        foreach ($matieres as $i => $matiere) {
            [$dayOffset, $h, $m, $duration] = $slots[$i % count($slots)];

            $debut = (clone $monday)->modify("+{$dayOffset} days")->setTime($h, $m);
            $fin = (clone $debut)->modify("+{$duration} minutes");

            $edt = (new EmploiDuTemps())
                ->setSalle('B' . rand(100, 300))
                ->setDateHeureDebut($debut)
                ->setDateHeureFin($fin)
                ->setMatiere($matiere)
            ;

            $manager->persist($edt);
        }
    }

    /**
     * Crée les comptes étudiants et retourne la liste.
     *
     * @return list<Etudiant>
     */
    private function addEtudiants(ObjectManager $manager): array
    {
        $data = [
            ['num' => 'E0123456789', 'nom' => 'Doe', 'prenom' => 'John', 'email' => 'john@doe.fr'],
            ['num' => 'E0234567890', 'nom' => 'Lambert', 'prenom' => 'Thomas', 'email' => 'lambert.thomas@etudiant.iut.fr'],
            ['num' => 'E0345678901', 'nom' => 'Dupuis', 'prenom' => 'Alice', 'email' => 'dupuis.alice@etudiant.iut.fr'],
            ['num' => 'E0456789012', 'nom' => 'Rousseau', 'prenom' => 'Maxime', 'email' => 'rousseau.maxime@etudiant.iut.fr'],
            ['num' => 'E0567890123', 'nom' => 'Moreau', 'prenom' => 'Céline', 'email' => 'moreau.celine@etudiant.iut.fr'],
        ];

        $etudiants = [];
        foreach ($data as $d) {
            $compte = new Compte();
            $compte->setEmail($d['email'])
                   ->setPassword($this->hasher->hashPassword($compte, 'Etudiant@1'))
                   ->setIsVerified(true)
                   ->setRoles(['ROLE_ETUDIANT'])
            ;

            $etudiant = (new Etudiant())
                ->setNumEtudiant($d['num'])
                ->setNom($d['nom'])
                ->setPrenom($d['prenom'])
                ->setAnnee(2026)
                ->setCompte($compte)
            ;

            $manager->persist($compte);
            $manager->persist($etudiant);
            $etudiants[] = $etudiant;
        }

        return $etudiants;
    }

    /**
     * Crée quelques notes pour peupler l'espace enseignant.
     *
     * @param list<Matiere> $matieres
     * @param list<Etudiant> $etudiants
     */
    private function addNotes(ObjectManager $manager, array $matieres, array $etudiants): void
    {
        // valeurs prédéfinies par étudiant × matière (5 étudiants × 5 premières matières)
        $valeurs = [
            [15.5, 13.0, 16.75, 12.5, 14.0],
            [11.0, 17.5, 9.25, 14.0, 16.0],
            [18.0, 12.0, 14.5, 10.0, 13.5],
            [8.5, 15.0, 11.75, 16.5, 12.0],
            [14.0, 10.5, 17.0, 13.25, 9.0],
        ];

        foreach ($etudiants as $ei => $etudiant) {
            foreach (array_slice($matieres, 0, 5) as $mi => $matiere) {
                $note = (new Note())
                    ->setValeur($valeurs[$ei][$mi])
                    ->setEtudiant($etudiant)
                    ->setMatiere($matiere)
                ;

                $manager->persist($note);
            }
        }
    }

    /**
     * Crée les comptes enseignants et retourne la liste.
     *
     * @return list<Enseignant>
     */
    private function addEnseignants(ObjectManager $manager): array
    {
        $data = [
            [
                'nom' => 'Dupont', 'prenom' => 'Jean',
                'specialite' => 'Électronique', 'bureau' => 'A101',
                'email' => 'dupont.jean@geii.fr',
            ],
            [
                'nom' => 'Martin', 'prenom' => 'Sophie',
                'specialite' => 'Systèmes Embarqués', 'bureau' => 'A102',
                'email' => 'martin.sophie@geii.fr',
            ],
            [
                'nom' => 'Bernard', 'prenom' => 'Thomas',
                'specialite' => 'Automatisme', 'bureau' => 'B201',
                'email' => 'bernard.thomas@geii.fr',
            ],
        ];

        $enseignants = [];
        foreach ($data as $d) {
            $compte = new Compte();
            $compte->setEmail($d['email'])
                   ->setPassword($this->hasher->hashPassword($compte, 'Enseignant@1'))
                   ->setIsVerified(true)
                   ->setRoles(['ROLE_ENSEIGNANT'])
            ;

            $enseignant = (new Enseignant())
                ->setNom($d['nom'])
                ->setPrenom($d['prenom'])
                ->setSpecialite($d['specialite'])
                ->setBureau($d['bureau'])
                ->setCompte($compte)
            ;

            $manager->persist($compte);
            $manager->persist($enseignant);
            $enseignants[] = $enseignant;
        }

        return $enseignants;
    }

    /**
     * Crée les entreprises partenaires et retourne la liste.
     *
     * @return list<Entreprise>
     */
    private function addEntreprises(ObjectManager $manager): array
    {
        $data = [
            [
                'nom' => 'Schneider Electric',
                'siret' => '54205985700013',
                'adresse' => '35 Rue Joseph Monier, 92500 Rueil-Malmaison',
                'secteur' => 'Énergie & Automatisme',
                'email' => 'contact@schneider-electric.fr',
            ],
            [
                'nom' => 'STMicroelectronics',
                'siret' => '38802105900036',
                'adresse' => '29 Blvd Romain Rolland, 75014 Paris',
                'secteur' => 'Semi-conducteurs',
                'email' => 'contact@st.com',
            ],
            [
                'nom' => 'Thales Group',
                'siret' => '34268680400066',
                'adresse' => '45 Rue de Villiers, 92200 Neuilly-sur-Seine',
                'secteur' => 'Défense & Électronique',
                'email' => 'contact@thalesgroup.fr',
            ],
        ];

        $entreprises = [];
        foreach ($data as $d) {
            $compte = new Compte();
            $compte->setEmail($d['email'])
                   ->setPassword($this->hasher->hashPassword($compte, 'Entreprise@1'))
                   ->setIsVerified(true)
                   ->setRoles(['ROLE_ENTREPRISE'])
            ;

            $entreprise = (new Entreprise())
                ->setNom($d['nom'])
                ->setSiret($d['siret'])
                ->setAdresse($d['adresse'])
                ->setSecteur($d['secteur'])
                ->setCompte($compte)
            ;

            $manager->persist($compte);
            $manager->persist($entreprise);
            $entreprises[] = $entreprise;
        }

        return $entreprises;
    }

    /**
     * Crée les offres d'alternance liées aux entreprises.
     *
     * @param list<Entreprise> $entreprises
     */
    private function addOffresAlternance(ObjectManager $manager, array $entreprises): void
    {
        $data = [
            [
                'titre' => 'Alternant Automatisme & Supervision',
                'description' => 'Conception et mise en œuvre de programmes PLC sur machines industrielles.',
                'duree' => 12,
                'statut' => StatutAlternance::ACTIVE,
                'entreprise' => $entreprises[0],
            ],
            [
                'titre' => 'Alternant Développement Embarqué',
                'description' => 'Développement de firmware en C/C++ pour microcontrôleurs STM32.',
                'duree' => 24,
                'statut' => StatutAlternance::ACTIVE,
                'entreprise' => $entreprises[1],
            ],
            [
                'titre' => 'Alternant Systèmes Électroniques de Défense',
                'description' => 'Intégration et tests de cartes électroniques embarquées pour systèmes avioniques.',
                'duree' => 12,
                'statut' => StatutAlternance::POURVUE,
                'entreprise' => $entreprises[2],
            ],
            [
                'titre' => 'Alternant Réseaux Industriels',
                'description' => 'Déploiement et supervision de réseaux Profibus/Profinet en environnement industriel.',
                'duree' => 12,
                'statut' => StatutAlternance::EXPIREE,
                'entreprise' => $entreprises[0],
            ],
        ];

        foreach ($data as $d) {
            $offre = (new OffreAlternance())
                ->setTitre($d['titre'])
                ->setDescription($d['description'])
                ->setDuree($d['duree'])
                ->setStatut($d['statut'])
                ->setEntreprise($d['entreprise'])
            ;

            $manager->persist($offre);
        }
    }

    /**
     * Crée les projets tuteurés liés aux entreprises et aux enseignants.
     *
     * @param list<Entreprise> $entreprises
     * @param list<Enseignant> $enseignants
     */
    private function addProjetsTutores(ObjectManager $manager, array $entreprises, array $enseignants): void
    {
        $data = [
            [
                'titre' => 'Système de supervision d\'atelier connecté',
                'description' => 'Développement d\'une interface SCADA légère pour la supervision d\'une ligne de production.',
                'annee' => 2026,
                'statut' => StatutProjetTuteure::EN_COURS,
                'entreprise' => $entreprises[0],
                'enseignant' => $enseignants[0],
            ],
            [
                'titre' => 'Carte de contrôle moteur STM32',
                'description' => 'Conception d\'une carte de pilotage de moteurs pas-à-pas basée sur un microcontrôleur STM32.',
                'annee' => 2026,
                'statut' => StatutProjetTuteure::OUVERT,
                'entreprise' => $entreprises[1],
                'enseignant' => $enseignants[1],
            ],
            [
                'titre' => 'Banc de test avionique automatisé',
                'description' => 'Automatisation d\'un banc de validation pour équipements embarqués de cockpit.',
                'annee' => 2025,
                'statut' => StatutProjetTuteure::TERMINE,
                'entreprise' => $entreprises[2],
                'enseignant' => $enseignants[2],
            ],
        ];

        foreach ($data as $d) {
            $projet = (new ProjetTuteure())
                ->setTitre($d['titre'])
                ->setDescription($d['description'])
                ->setAnnee($d['annee'])
                ->setStatut($d['statut'])
                ->setEntreprise($d['entreprise'])
                ->setEnseignantTuteur($d['enseignant'])
            ;

            $manager->persist($projet);
        }
    }

    /**
     * Crée quelques supports de cours.
     *
     * @throws DateMalformedStringException
     */
    private function addSupportsCours(ObjectManager $manager): void
    {
        $data = [
            [
                'titre' => 'Introduction aux automates programmables',
                'fichier' => 'cours_automates.pdf',
                'date' => '2026-02-10'
            ], [
                'titre' => 'Microcontrôleurs STM32 – prise en main',
                'fichier' => 'cours_stm32.pdf',
                'date' => '2026-02-17'
            ], [
                'titre' => 'Réseaux Profibus & Profinet',
                'fichier' => 'cours_reseaux_indus.pdf',
                'date' => '2026-03-03'
            ], [
                'titre' => 'Filtres analogiques et numériques',
                'fichier' => 'cours_filtres.pdf',
                'date' => '2026-03-10'
            ],
        ];

        foreach ($data as $d) {
            $support = (new SupportCours())
                ->setTitre($d['titre'])
                ->setFichierPath($d['fichier'])
                ->setDateDepot(new DateTime($d['date']))
            ;

            $manager->persist($support);
        }
    }

    /**
     * Crée l'unique compte personnel (administrateur).
     */
    private function addPersonnel(ObjectManager $manager): void
    {
        $compte = new Compte();
        $compte->setEmail('admin@geii.fr')
                   ->setPassword($this->hasher->hashPassword($compte, 'Admin@geii1'))
               ->setIsVerified(true)
               ->setRoles(['ROLE_PERSONNEL'])
        ;

        $personnel = (new Personnel())
            ->setNom('Girard')
            ->setPrenom('Sophie')
            ->setFonction('Responsable pédagogique')
            ->setAdmin(true)
            ->setCompte($compte)
        ;

        $manager->persist($compte);
        $manager->persist($personnel);
    }
}
