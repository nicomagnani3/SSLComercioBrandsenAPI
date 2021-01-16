<?php

namespace App\Repository;

use App\Entity\Emprendimientos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Emprendimientos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emprendimientos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emprendimientos[]    findAll()
 * @method Emprendimientos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmprendimientosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Emprendimientos::class);
    }

    // /**
    //  * @return Emprendimientos[] Returns an array of Emprendimientos objects
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
    public function findOneBySomeField($value): ?Emprendimientos
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
