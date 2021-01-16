<?php


namespace App\AbstractClass;
abstract class ServicioAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            
           
        ];  
    }
}