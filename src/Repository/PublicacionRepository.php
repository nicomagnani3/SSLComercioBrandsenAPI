<?php

namespace App\Repository;

use App\Entity\Publicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Publicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publicacion[]    findAll()
 * @method Publicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicacionRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicacion::class);
    }

    public function getPublicacionesPorTitulo($titulo, $em)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =    "SELECT P.id,P.idusuario_id,P.fecha,P.titulo,P.descripcion,P.precio,P.categoria_id,P.categoria_hija_id,P.destacada
        FROM Publicacion P 
           inner join categorias C on p.categoria_id = c.id
           inner join categorias_hijas CH on p.categoria_hija_id =ch.id
		   left join Empresa e on p.idusuario_id = e.usuarios_id 
           where p.pago is not null and ( p.titulo COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
           or c.nombre COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
		   or e.nombre COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
           or ch.descripcion COLLATE SQL_Latin1_General_Cp1_CI_AI LIKE CONCAT('%','$titulo','%')
           or p.titulo LIKE '%$titulo%')
            order by p.fecha DESC   
           SET NOCOUNT ON;";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function borrarPublicacion($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM Publicacion 
                                 where id ='$id'";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
	public function getpubliacionpaginate($page){		
				$page= intval($page);
				$conn = $this->getEntityManager()->getConnection();
				$query="DECLARE @PageNumber AS INT
				DECLARE @RowsOfPage AS INT
				SET @PageNumber=$page
				SET @RowsOfPage=20
				SELECT * FROM Publicacion
				WHERE (destacada IS NULL or destacada = 0)
				AND pago = 1
				ORDER BY fecha DESC
				OFFSET ((@PageNumber-1)*@RowsOfPage) ROWS
				FETCH NEXT @RowsOfPage ROWS ONLY";
			
                  $stmt = $conn->prepare($query);
                  $stmt->execute();
        return $stmt->fetchAll();
    }
	public function cantidadPublicacionesNormales(){
			$conn = $this->getEntityManager()->getConnection();
			$query="SELECT count(*) as cantidad
				from Publicacion 
				WHERE (destacada IS NULL or destacada = 0)
				AND pago = 1";
			$stmt = $conn->prepare($query);
            $stmt->execute();
        return $stmt->fetchAll();	
	}
}
