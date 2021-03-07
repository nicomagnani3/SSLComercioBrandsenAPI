<?php


namespace App\AbstractClass;
abstract class RubrosAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            
           
        ];  
    }
}