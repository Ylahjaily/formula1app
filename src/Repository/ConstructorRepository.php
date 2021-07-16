<?php

namespace App\Repository;

use App\Entity\Constructor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Constructor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Constructor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Constructor[]    findAll()
 * @method Constructor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstructorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Constructor::class);
    }

    // /**
    //  * @return Constructor[] Returns an array of Constructor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Constructor
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
