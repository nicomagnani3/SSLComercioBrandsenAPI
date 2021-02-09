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

        $query =    "SELECT PS.id,PS.idusuario_id,PS.fecha,PS.titulo,PS.descripcion,PS.precio,PS.servicio_id_id,PS.serviciohijo_id_id,PS.destacada
        FROM Publicacion_servicios PS 
               inner join Servicios S on PS.servicio_id_id = S.id
               inner join servicios_hijos SH on PS.serviciohijo_id_id = SH.id
        
        where ps.descripcion COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
        or S.nombre COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
        or SH.descripcion COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
        or PS.titulo LIKE '%$titulo%'
        order by PS.fecha DESC
        SET NOCOUNT ON";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function borrarPublicacion($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM Publicacion_servicios 
                                 where id ='$id'";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
