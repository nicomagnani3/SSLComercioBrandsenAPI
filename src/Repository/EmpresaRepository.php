<?php

namespace App\Repository;

use App\Entity\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Empresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empresa[]    findAll()
 * @method Empresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function getPublicacionesEmpresa($empresa)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =    "SELECT p.id, p.fecha,p.precio,p.titulo,p.descripcion,p.destacada ,u.telefono, cat.nombre as padre, u.email, u.web
           FROM Empresa E 
           INNER JOIN Publicacion P on E.usuarios_id = P.idusuario_id
           INNER JOIN Contratos C on E.usuarios_id = c.usuario_id
           INNER JOIN usuarios u on u.id = E.usuarios_id 
           INNER JOIN Categorias cat on cat.id = p.categoria_id
           where e.usuarios_id = '$empresa' and c.pago = 1 
           order by p.fecha desc";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
