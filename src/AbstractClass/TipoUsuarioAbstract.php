<?php


namespace App\AbstractClass;
abstract class TipoUsuarioAbstract
{
    public function getArray()
    {
   
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getDescripcion(),
            
           
        ];  
    }
}