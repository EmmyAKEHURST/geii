<?php

namespace App\Tests\Repository;

use App\Entity\EmploiDuTemps;
use App\Repository\EmploiDuTempsRepository;
use App\Tests\IntegrationTestCase;
use DateMalformedStringException;
use DateTime;

class EmploiDuTempsRepositoryTest extends IntegrationTestCase
{
    private EmploiDuTempsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->em->getRepository(EmploiDuTemps::class);
    }

    private function createCours(DateTime $debut, DateTime $fin, string $salle): void
    {
        $cours = (new EmploiDuTemps())
            ->setDateHeureDebut($debut)
            ->setDateHeureFin($fin)
            ->setSalle($salle)
        ;

        $this->em->persist($cours);
    }

    // ── getPlanningForToday ──────────────────────────────────────────────────

    /**
     * Vérifie que getPlanningForToday retourne un tableau vide en l'absence de cours.
     */
    public function testGetPlanningForTodayRetourneTableauVideSiAucunCours(): void
    {
        $this->assertSame([], $this->repository->getPlanningForToday());
    }

    /**
     * Vérifie que getPlanningForToday retourne les cours du jour courant.
     */
    public function testGetPlanningForTodayRetourneLesCoursDuJour(): void
    {
        $this->createCours(
            (new DateTime())->setTime(8, 0),
            (new DateTime())->setTime(10, 0),
            'A101'
        );

        $this->em->flush();

        $result = $this->repository->getPlanningForToday();

        $this->assertCount(1, $result);
        $this->assertSame('A101', $result[0]->getSalle());
    }

    /**
     * Vérifie que getPlanningForToday exclut les cours des autres jours.
     */
    public function testGetPlanningForTodayExclutLesAutresJours(): void
    {
        // Cours hier → exclu
        $hier = new DateTime('yesterday');
        $this->createCours(
            (clone $hier)->setTime(8, 0),
            (clone $hier)->setTime(10, 0),
            'Hier'
        );

        // Cours demain → exclu
        $demain = new DateTime('tomorrow');
        $this->createCours(
            (clone $demain)->setTime(8, 0),
            (clone $demain)->setTime(10, 0),
            'Demain'
        );

        // Cours aujourd'hui → inclus
        $this->createCours(
            (new DateTime())->setTime(14, 0),
            (new DateTime())->setTime(16, 0),
            "Aujourd'hui"
        );

        $this->em->flush();

        $result = $this->repository->getPlanningForToday();

        $this->assertCount(1, $result);
        $this->assertSame("Aujourd'hui", $result[0]->getSalle());
    }

    /**
     * Vérifie que getPlanningForToday trie les cours par heure de début croissante.
     */
    public function testGetPlanningForTodayTrieParHeureDeDebut(): void
    {
        $this->createCours(
            (new DateTime())->setTime(14, 0),
            (new DateTime())->setTime(16, 0),
            'Après-midi'
        );

        $this->createCours(
            (new DateTime())->setTime(8, 0),
            (new DateTime())->setTime(10, 0),
            'Matin'
        );

        $this->em->flush();

        $result = $this->repository->getPlanningForToday();

        $this->assertCount(2, $result);
        $this->assertSame('Matin', $result[0]->getSalle());
        $this->assertSame('Après-midi', $result[1]->getSalle());
    }

    // ── getWeeklyPlanning ────────────────────────────────────────────────────

    /**
     * Vérifie que getWeeklyPlanning retourne les 5 jours de la semaine avec des listes vides.
     *
     * @throws DateMalformedStringException
     */
    public function testGetWeeklyPlanningRetourneLes5JoursVides(): void
    {
        $result = $this->repository->getWeeklyPlanning();

        $this->assertArrayHasKey('lundi', $result);
        $this->assertArrayHasKey('mardi', $result);
        $this->assertArrayHasKey('mercredi', $result);
        $this->assertArrayHasKey('jeudi', $result);
        $this->assertArrayHasKey('vendredi', $result);
        $this->assertCount(5, $result);

        foreach ($result as $jour) {
            $this->assertSame([], $jour);
        }
    }

    /**
     * Vérifie que getWeeklyPlanning regroupe correctement les cours par jour.
     *
     * @throws DateMalformedStringException
     */
    public function testGetWeeklyPlanningGroupeLesCoursByJour(): void
    {
        // Lundi de cette semaine
        $lundi = (new DateTime())->modify('monday this week');
        $this->createCours(
            (clone $lundi)->setTime(8, 0),
            (clone $lundi)->setTime(10, 0),
            'Lundi matin'
        );

        // Mercredi de cette semaine
        $mercredi = (new DateTime())->modify('wednesday this week');
        $this->createCours(
            (clone $mercredi)->setTime(14, 0),
            (clone $mercredi)->setTime(16, 0),
            'Mercredi après-midi'
        );

        $this->em->flush();

        $result = $this->repository->getWeeklyPlanning();

        $this->assertCount(1, $result['lundi']);
        $this->assertCount(0, $result['mardi']);
        $this->assertCount(1, $result['mercredi']);
        $this->assertCount(0, $result['jeudi']);
        $this->assertCount(0, $result['vendredi']);
        $this->assertSame('Lundi matin', $result['lundi'][0]->getSalle());
        $this->assertSame('Mercredi après-midi', $result['mercredi'][0]->getSalle());
    }

    /**
     * Vérifie que getWeeklyPlanning exclut les cours du week-end.
     *
     * @throws DateMalformedStringException
     */
    public function testGetWeeklyPlanningExclutLeWeekEnd(): void
    {
        // Samedi de cette semaine → hors plage lundi-vendredi
        $samedi = (new DateTime())->modify('saturday this week');
        $this->createCours(
            (clone $samedi)->setTime(9, 0),
            (clone $samedi)->setTime(11, 0),
            'Samedi'
        );
        $this->em->flush();

        $result = $this->repository->getWeeklyPlanning();

        foreach ($result as $jour) {
            $this->assertCount(0, $jour);
        }
    }

    /**
     * Vérifie que getWeeklyPlanning gère plusieurs cours par jour et les trie par heure.
     *
     * @throws DateMalformedStringException
     */
    public function testGetWeeklyPlanningPlusieursCoursParJour(): void
    {
        $vendredi = (new DateTime())->modify('friday this week');

        $this->createCours(
            (clone $vendredi)->setTime(14, 0),
            (clone $vendredi)->setTime(16, 0),
            'Vendredi PM'
        );

        $this->createCours(
            (clone $vendredi)->setTime(8, 0),
            (clone $vendredi)->setTime(10, 0),
            'Vendredi AM'
        );

        $this->em->flush();

        $result = $this->repository->getWeeklyPlanning();

        $this->assertCount(2, $result['vendredi']);

        // Triés par heure de début ASC
        $this->assertSame('Vendredi AM', $result['vendredi'][0]->getSalle());
        $this->assertSame('Vendredi PM', $result['vendredi'][1]->getSalle());
    }
}
