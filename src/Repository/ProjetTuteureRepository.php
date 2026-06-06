<?php

namespace App\Repository;

use App\Entity\Entreprise;
use App\Entity\ProjetTuteure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjetTuteure>
 */
class ProjetTuteureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjetTuteure::class);
    }

    /**
     * @param Entreprise $entreprise
     * @return list<ProjetTuteure>
     */
    public function findByEntrepriseOrdered(Entreprise $entreprise): array
    {
        /** @var list<ProjetTuteure> $result */
        $result =  $this->createQueryBuilder('p')
            ->andWhere('p.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @param Entreprise $entreprise
     * @return int
     */
    public function countByEntreprise(Entreprise $entreprise): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
