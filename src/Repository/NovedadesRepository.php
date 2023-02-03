<?php

namespace App\Repository;

use App\Entity\Novedades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Novedades|null find($id, $lockMode = null, $lockVersion = null)
 * @method Novedades|null findOneBy(array $criteria, array $orderBy = null)
 * @method Novedades[]    findAll()
 * @method Novedades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NovedadesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Novedades::class);
    }
    public function borrar($id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query =  "DELETE FROM novedades 
                                 where id ='$id'";        
             
        $stmt = $conn->prepare($query);       
        $stmt->execute();
        return $stmt->fetchAll();
     
    }
    
}
