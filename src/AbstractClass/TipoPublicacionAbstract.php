<?php


namespace App\AbstractClass;
abstract class TipoPublicacionAbstract
{
    public function getArray()
    {
   
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            
           
        ];  
    }
}