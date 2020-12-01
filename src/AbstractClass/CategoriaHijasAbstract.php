<?php


namespace App\AbstractClass;
abstract class CategoriaHijasAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getDescripcion(),
            
           
        ];  
    }
}