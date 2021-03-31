<?php

namespace App\Repository;

use App\Entity\Publicidades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publicidades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicidades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicidades[]    findAll()
 * @method Publicidades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicidadesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicidades::class);
    }

    // /**
    //  * @return Publicidades[] Returns an array of Publicidades objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Publicidades
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
