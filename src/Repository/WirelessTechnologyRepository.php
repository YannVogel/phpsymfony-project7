<?php

namespace App\Repository;

use App\Entity\WirelessTechnology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WirelessTechnology|null find($id, $lockMode = null, $lockVersion = null)
 * @method WirelessTechnology|null findOneBy(array $criteria, array $orderBy = null)
 * @method WirelessTechnology[]    findAll()
 * @method WirelessTechnology[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WirelessTechnologyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WirelessTechnology::class);
    }

    // /**
    //  * @return WirelessTechnology[] Returns an array of WirelessTechnology objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WirelessTechnology
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
