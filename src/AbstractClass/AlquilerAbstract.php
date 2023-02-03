<?php


namespace App\AbstractClass;
abstract class AlquilerAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),         
                  

			
        ];  
    }
	   public function crearPublicacion(
        $propiedad,
        $operacion,
        $observaciones,
        $cordeanadas,
        $usuarioID) {
        $this->setPropiedad($propiedad);         
        $this->setOperacion($operacion);               
        $this->setObservaciones($observaciones);     
        $this->setCoordenadas($cordeanadas);                
        $this->setIDusuario($usuarioID);
      
    }
}