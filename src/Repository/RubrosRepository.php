<?php

namespace App\Repository;

use App\Entity\Rubros;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rubros|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rubros|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rubros[]    findAll()
 * @method Rubros[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RubrosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rubros::class);
    }
    public function getEmpresasConContratoYRubro($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "SELECT e.id as idEmpresa, e.nombre as nombreEmpresa, u.username as nombreUsuario, u.telefono as telefono
        FROM Contratos c
        INNER JOIN usuarios u on u.id = c.usuario_id
        INNER JOIN Empresa e on e.usuarios_id = u.id
        INNER JOIN Rubros r on r.id = e.rubro_id_id
		INNER JOIN Publicacion p on p.idusuario_id = u.id
        where c.hasta > GETDATE() and r.id ='$id'
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
