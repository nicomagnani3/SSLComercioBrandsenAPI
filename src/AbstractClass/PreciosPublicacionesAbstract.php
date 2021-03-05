<?php


namespace App\AbstractClass;
abstract class PreciosPublicacionesAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),           
            'precio'=>$this->getPrecio(),
         
            
        ];  
    }
 

    

     
}