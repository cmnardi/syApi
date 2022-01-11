<?php

namespace App\Repository;

use App\Entity\Blaz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Blaz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blaz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blaz[]    findAll()
 * @method Blaz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlazRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blaz::class);
    }

    // /**
    //  * @return Blaz[] Returns an array of Blaz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Blaz
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
