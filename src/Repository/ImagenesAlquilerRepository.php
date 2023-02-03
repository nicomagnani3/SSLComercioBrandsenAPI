<?php

namespace App\Repository;

use App\Entity\ImagenesAlquiler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ImagenesPublicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagenesPublicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagenesPublicacion[]    findAll()
 * @method ImagenesPublicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenesAlquilerRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagenesAlquiler::class);
    }

    
    public function borrarImagen($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM imagenes_alquiler
                                 where id ='$id'";        
             
        $stmt = $conn->prepare($query);       
        $stmt->execute();
        return $stmt->fetchAll();
     
    }
}
