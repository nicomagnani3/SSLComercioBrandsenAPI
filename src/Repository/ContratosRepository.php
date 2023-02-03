<?php

namespace App\Repository;

use App\Entity\Contratos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contratos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contratos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contratos[]    findAll()
 * @method Contratos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contratos::class);
    }

    public function listadoContratosClientes()
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =    "SELECT c.id,c.desde,c.hasta,p.nombre as paquete,c.cant_destacadas as cantDestacada, c.cant_publicaciones as cantnormal, c.pago, u.email as userMail, u.telefono as userCel,tu.descripcion as userTipo
       ,CONCAT (cli.apellido , + ' ' + cli.nombre) as nombre
	   FROM Contratos	c	
        inner join Cliente cli on cli.usuarios_id= c.usuario_id
        inner join usuarios u on u.id = c.usuario_id
        inner join tipos_usuarios tu on tu.id = u.tipousuario_id_id
        inner join Paquete p on p.id = c.paquete_id
        where pago = 1
        order by  c.hasta ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function listadoContratosEmpresas()
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =    "SELECT c.id,c.desde,c.hasta,p.nombre as paquete,c.cant_destacadas as cantDestacada, c.cant_publicaciones as cantnormal, c.pago, u.email as userMail, u.telefono as userCel,tu.descripcion as userTipo
		, cli.nombre
	   FROM Contratos	c	        
        inner join empresa cli on cli.usuarios_id= c.usuario_id
        inner join usuarios u on u.id = c.usuario_id
        inner join tipos_usuarios tu on tu.id = u.tipousuario_id_id
        inner join Paquete p on p.id = c.paquete_id
        where pago = 1
        order by  c.hasta ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
