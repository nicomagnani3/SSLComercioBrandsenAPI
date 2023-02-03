<?php

namespace App\Repository;

use App\Entity\Utilidades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Utilidades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilidades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilidades[]    findAll()
 * @method Utilidades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilidadesRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilidades::class);
    }
 
}
