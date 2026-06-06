<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\OffreAlternance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreAlternance>
 */
class OffreAlternanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreAlternance::class);
    }

    /**
     * @param Entreprise $entreprise
     *
     * @return list<OffreAlternance>
     */
    public function findByEntrepriseOrdered(Entreprise $entreprise): array
    {
        /** @var list<OffreAlternance> $result */
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @param Entreprise $entreprise
     *
     * @return int
     */
    public function countByEntreprise(Entreprise $entreprise): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
