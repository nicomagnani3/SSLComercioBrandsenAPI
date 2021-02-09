<?php

namespace App\Repository;

use App\Entity\PublicacionEmprendimientos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PublicacionEmprendimientos|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicacionEmprendimientos|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicacionEmprendimientos[]    findAll()
 * @method PublicacionEmprendimientos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionEmprendimientosRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicacionEmprendimientos::class);
    }

    public function getPublicacionesPorTitulo($titulo, $em)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =    "SELECT PE.id,PE.idusuari_id_id,PE.fecha,PE.titulo,PE.descripcion,PE.precio,PE.emprendimiento_id,PE.destacada
        FROM Publicacion_emprendimientos PE 
         inner join Emprendimientos E on PE.emprendimiento_id = E.id
         
         where pe.descripcion COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
         or e.nombre COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
         or pe.titulo LIKE '%$titulo%'
         order by pe.fecha DESC
         SET NOCOUNT ON;";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function borrarPublicacion($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM Publicacion_emprendimientos 
                                 where id ='$id'";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
