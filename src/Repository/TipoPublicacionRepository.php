<?php

namespace App\Repository;

use App\Entity\TipoPublicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TipoPublicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoPublicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoPublicacion[]    findAll()
 * @method TipoPublicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoPublicacionRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoPublicacion::class);
    }

    // /**
    //  * @return TipoPublicacion[] Returns an array of TipoPublicacion objects
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
    public function findOneBySomeField($value): ?TipoPublicacion
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
