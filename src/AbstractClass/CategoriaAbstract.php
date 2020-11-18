<?php


namespace App\AbstractClass;
abstract class CategoriaAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            
           
        ];  
    }
}