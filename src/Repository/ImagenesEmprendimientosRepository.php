<?php

namespace App\Repository;

use App\Entity\ImagenesEmprendimientos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ImagenesEmprendimientos|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagenesEmprendimientos|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagenesEmprendimientos[]    findAll()
 * @method ImagenesEmprendimientos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenesEmprendimientosRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ImagenesEmprendimientos::class);
    }

    // /**
    //  * @return ImagenesEmprendimientos[] Returns an array of ImagenesEmprendimientos objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImagenesEmprendimientos
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
