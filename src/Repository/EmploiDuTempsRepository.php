<?php

namespace App\Repository;

use DateTime;
use App\Entity\EmploiDuTemps;
use DateMalformedStringException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<EmploiDuTemps>
 */
class EmploiDuTempsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmploiDuTemps::class);
    }

    /**
     * Retourne les cours pour le planning d'aujourd'hui.
     *
     * @return EmploiDuTemps[]
     */
    public function getPlanningForToday(): array
    {
        // Date : aujourd'hui.
        $dateDebut = (new DateTime())->setTime(0, 0);
        $dateFin = (new DateTime())->setTime(23, 59, 59);

        /** @var EmploiDuTemps[] $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.date_heure_debut >= :dateDebut')
            ->andWhere('e.date_heure_fin <= :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('e.date_heure_debut', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * Retourne les cours de la semaine courante, ces cours sont groupés par jour.
     *
     * @return array<string, list<mixed>>
     * @throws DateMalformedStringException
     */
    public function getWeeklyPlanning(): array
    {
        $monday = (new DateTime())->modify('monday this week')->setTime(0, 0);
        $friday = (new DateTime())->modify('friday this week')->setTime(23, 59, 59);

        /** @var array<EmploiDuTemps> $entries */
        $entries = $this->createQueryBuilder('e')
            ->where('e.date_heure_debut >= :monday')
            ->andWhere('e.date_heure_fin <= :friday')
            ->setParameter('monday', $monday)
            ->setParameter('friday', $friday)
            ->orderBy('e.date_heure_debut', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $dayNames = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];

        /** @var array<string, list<mixed>> $grouped */
        $grouped = array_fill_keys($dayNames, []);

        foreach ($entries as $entry) {
            $dayNum = (int) $entry->getDateHeureDebut()?->format('N');

            if (isset($dayNames[$dayNum - 1])) {
                $grouped[$dayNames[$dayNum - 1]][] = $entry;
            }
        }

        return $grouped;
    }
}
