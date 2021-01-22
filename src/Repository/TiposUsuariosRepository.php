<?php

namespace App\Repository;

use App\Entity\TiposUsuarios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TiposUsuarios|null find($id, $lockMode = null, $lockVersion = null)
 * @method TiposUsuarios|null findOneBy(array $criteria, array $orderBy = null)
 * @method TiposUsuarios[]    findAll()
 * @method TiposUsuarios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TiposUsuariosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TiposUsuarios::class);
    }

    // /**
    //  * @return TiposUsuarios[] Returns an array of TiposUsuarios objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TiposUsuarios
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
