<?php

namespace App\Repository;

use App\Entity\ScreenResolution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScreenResolution|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScreenResolution|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScreenResolution[]    findAll()
 * @method ScreenResolution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenResolutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScreenResolution::class);
    }

    // /**
    //  * @return ScreenResolution[] Returns an array of ScreenResolution objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ScreenResolution
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
