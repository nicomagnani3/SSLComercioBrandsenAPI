<?php

namespace App\Repository;

use App\Entity\PreciosPublicaciones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PreciosPublicaciones|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreciosPublicaciones|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreciosPublicaciones[]    findAll()
 * @method PreciosPublicaciones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreciosPublicacionesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreciosPublicaciones::class);
    }

    // /**
    //  * @return PreciosPublicaciones[] Returns an array of PreciosPublicaciones objects
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
    public function findOneBySomeField($value): ?PreciosPublicaciones
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
