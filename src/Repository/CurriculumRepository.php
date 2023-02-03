<?php

namespace App\Repository;

use App\Entity\Curriculum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CategoriasHijas|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoriasHijas|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoriasHijas[]    findAll()
 * @method CategoriasHijas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurriculumRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Curriculum::class);
    }

    // /**
    //  * @return CategoriasHijas[] Returns an array of CategoriasHijas objects
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
    public function findOneBySomeField($value): ?CategoriasHijas
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