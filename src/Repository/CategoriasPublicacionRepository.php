<?php

namespace App\Repository;

use App\Entity\CategoriasPublicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CategoriasPublicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoriasPublicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoriasPublicacion[]    findAll()
 * @method CategoriasPublicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriasPublicacionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CategoriasPublicacion::class);
    }

    // /**
    //  * @return CategoriasPublicacion[] Returns an array of CategoriasPublicacion objects
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
    public function findOneBySomeField($value): ?CategoriasPublicacion
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
