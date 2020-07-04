<?php

namespace App\Repository;

use App\Entity\SimSize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SimSize|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimSize|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimSize[]    findAll()
 * @method SimSize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimSizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimSize::class);
    }

    // /**
    //  * @return SimSize[] Returns an array of SimSize objects
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
    public function findOneBySomeField($value): ?SimSize
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
