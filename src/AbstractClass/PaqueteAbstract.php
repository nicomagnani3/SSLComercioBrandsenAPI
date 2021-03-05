<?php


namespace App\AbstractClass;
abstract class PaqueteAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            'cantNormal' => $this->getCantNormal(),
            'cantDestacada'=>$this->getCantDestacada(),
            'precio'=>$this->getPrecio(),
            'tipo'=>$this->getTipo(),
            
        ];  
    }
 

    

     
}
