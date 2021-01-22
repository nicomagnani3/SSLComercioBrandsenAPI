<?php

namespace App\Repository;

use App\Entity\PublicacionServicios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PublicacionServicios|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicacionServicios|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicacionServicios[]    findAll()
 * @method PublicacionServicios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionServiciosRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicacionServicios::class);
    }
    public function getPublicacionesPorTitulo($titulo, $em)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =    "SELECT * from Publicacion_servicios where titulo LIKE '%$titulo%'
                        order by fecha DESC";       
        $stmt = $conn->prepare($query);       
        $stmt->execute();
        return $stmt->fetchAll();
     
    }

    
}
