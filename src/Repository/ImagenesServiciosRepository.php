<?php

namespace App\Repository;

use App\Entity\ImagenesServicios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ImagenesServicios|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagenesServicios|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagenesServicios[]    findAll()
 * @method ImagenesServicios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenesServiciosRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagenesServicios::class);
    }

    // /**
    //  * @return ImagenesServicios[] Returns an array of ImagenesServicios objects
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
    public function findOneBySomeField($value): ?ImagenesServicios
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
