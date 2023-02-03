<?php

namespace App\Repository;

use App\Entity\Alquiler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CategoriasHijas|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoriasHijas|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoriasHijas[]    findAll()
 * @method CategoriasHijas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlquilerRepository extends ServiceEntityRepository
{
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, Alquiler::class);
    }

 public function getpubliacionpaginate($page){
        $conn = $this->getEntityManager()->getConnection();
        $query="SELECT * FROM alquiler                
                ORDER BY id DESC
                OFFSET ($page-1)*15 ROWS
                FETCH NEXT 15 ROWS ONLY";
                  $stmt = $conn->prepare($query);
                  $stmt->execute();
                  return $stmt->fetchAll();

    }
		public function cantidadPublicacionesNormales(){
			$conn = $this->getEntityManager()->getConnection();
			$query="SELECT count(*) as cantidad
				from alquiler 
				";
			$stmt = $conn->prepare($query);
            $stmt->execute();
        return $stmt->fetchAll();	
	}
}
