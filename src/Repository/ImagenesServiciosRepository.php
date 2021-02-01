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

    public function borrarImagen($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM imagenes_servicios
                                 where id ='$id'";        
             
        $stmt = $conn->prepare($query);       
        $stmt->execute();
        return $stmt->fetchAll();
     
    }
}
