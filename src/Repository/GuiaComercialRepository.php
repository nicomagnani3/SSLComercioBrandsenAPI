<?php

namespace App\Repository;

use App\Entity\GuiaComercial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GuiaComercial|null find($id, $lockMode = null, $lockVersion = null)
 * @method GuiaComercial|null findOneBy(array $criteria, array $orderBy = null)
 * @method GuiaComercial[]    findAll()
 * @method GuiaComercial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GuiaComercialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GuiaComercial::class);
    }

    // /**
    //  * @return GuiaComercial[] Returns an array of GuiaComercial objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GuiaComercial
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
