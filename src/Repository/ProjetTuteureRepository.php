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
     * @return list<ProjetTuteure>
     */
    public function findByEntrepriseOrdered(Entreprise $entreprise): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByEntreprise(Entreprise $entreprise): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.entreprise = :entreprise')
            ->setParameter('entreprise', $entreprise)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return ProjetTuteure[] Returns an array of ProjetTuteure objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ProjetTuteure
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
