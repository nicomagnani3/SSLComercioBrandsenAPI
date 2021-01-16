<?php

namespace App\Repository;

use App\Entity\ServiciosHijos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ServiciosHijos|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiciosHijos|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiciosHijos[]    findAll()
 * @method ServiciosHijos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiciosHijosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ServiciosHijos::class);
    }

    // /**
    //  * @return ServiciosHijos[] Returns an array of ServiciosHijos objects
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
    public function findOneBySomeField($value): ?ServiciosHijos
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
