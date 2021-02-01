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
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagenesEmprendimientos::class);
    }
    public function borrarImagen($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM imagenes_emprendimientos 
                                 where id ='$id'";        
             
        $stmt = $conn->prepare($query);       
        $stmt->execute();
        return $stmt->fetchAll();
     
    }
}
