<?php

namespace App\Repository;

use App\Entity\EmprendimientosHijos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EmprendimientosHijos|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmprendimientosHijos|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmprendimientosHijos[]    findAll()
 * @method EmprendimientosHijos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmprendimientosHijosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EmprendimientosHijos::class);
    }

    // /**
    //  * @return EmprendimientosHijos[] Returns an array of EmprendimientosHijos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmprendimientosHijos
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
