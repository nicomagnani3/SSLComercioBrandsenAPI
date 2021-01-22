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

        $query =    "SELECT * from Publicacion_emprendimientos where titulo LIKE '%$titulo%'
                        order by fecha DESC";       
        $stmt = $conn->prepare($query);       
        $stmt->execute();
        return $stmt->fetchAll();
     
    }
}
