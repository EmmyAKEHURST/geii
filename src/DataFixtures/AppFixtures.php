<?php

namespace App\DataFixtures;

use App\Entity\Compte;
use App\Entity\Entreprise;
use App\Entity\Etudiant;
use DateMalformedStringException;
use DateTime;
use App\Entity\Matiere;
use App\Entity\EmploiDuTemps;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @throws DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        $this->addStudentAccount($manager);
        $this->addCompanyAccount($manager);

        $monday = (new DateTime('monday this week'));
        $nomMatieres = $this->getListMatieres();

        // [dayOffset, startHour, startMin, durationMinutes] — 8 slots = 8 matières, lundi(0)→vendredi(4)
        $slots = [
            [0, 9, 0, 90], [0, 13, 0, 120],
            [1, 9, 0, 120], [1, 14, 0, 90],
            [2, 10, 0, 90],
            [3, 9, 0, 60], [3, 13, 0, 120],
            [4, 9, 0, 90],
        ];

        foreach ($nomMatieres as $i => $nom) {
            [$dayOffset, $h, $m, $duration] = $slots[$i % count($slots)];

            $debut = (clone $monday)->modify("+{$dayOffset} days")->setTime($h, $m);
            $fin = (clone $debut)->modify("+{$duration} minutes");

            $edt = (new EmploiDuTemps())
                ->setSalle('B' . rand(100, 300))
                ->setDateHeureDebut($debut)
                ->setDateHeureFin($fin)
            ;

            $manager->persist($edt);

            $manager->persist((new Matiere())->addEmploiDuTemps($edt)->setNom($nom));
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    private function addStudentAccount(ObjectManager $manager): void
    {
        $user = (new Compte())
            ->setEmail("john@doe.fr")
            ->setPassword("JohnDoe@1")
            ->setIsVerified(true)
            ->setRoles(["ROLE_ETUDIANT"])
        ;

        $etudiant = (new Etudiant())
            ->setNumEtudiant("E0123456789")
            ->setNom("John")
            ->setPrenom("Doe")
            ->setAnnee(2026)
            ->setCompte($user)
        ;

        $manager->persist($user);
        $manager->persist($etudiant);
    }

    private function addCompanyAccount(ObjectManager $manager): void
    {
        $user = (new Compte())
            ->setEmail('partenaire@geii.fr')
            ->setPassword('Partenaire@1')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ENTREPRISE'])
        ;

        $entreprise = (new Entreprise())
            ->setNom('Siemens France')
            ->setSiret('12345678901234')
            ->setSecteur('Industrie / Électronique')
            ->setAdresse('12 avenue de l\'Innovation, 69000 Lyon')
            ->setCompte($user)
        ;

        $manager->persist($user);
        $manager->persist($entreprise);
    }

    /**
     * @return list<string>
     */
    private function getListMatieres(): array
    {
        return [
            'Électronique Numérique',
            'Systèmes Embarqués',
            'Automatisme',
            'Projet Tuteuré',
            'Mathématiques Appliquées',
            'Communication Professionnelle',
            'Réseaux Industriels',
            'TP Électronique',
        ];
    }
}
