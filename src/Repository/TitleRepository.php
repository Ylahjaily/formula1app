<?php

namespace App\Repository;

use App\Entity\Title;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Title|null find($id, $lockMode = null, $lockVersion = null)
 * @method Title|null findOneBy(array $criteria, array $orderBy = null)
 * @method Title[]    findAll()
 * @method Title[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Title::class);
    }

    // /**
    //  * @return Object Returns an object of constructors names with theirs number of titles
    //  */
    public function countConstructorsTitles()
    {
        $rawSql = "SELECT `name`, COUNT(*) as 'titles' FROM `title`, `constructor` WHERE `constructor_id` = constructor.id GROUP BY `name` ORDER BY titles DESC";

        $statement = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $statement->execute([]);

        return $statement->fetchAll();
    }

    /*
    public function findOneBySomeField($value): ?Title
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
