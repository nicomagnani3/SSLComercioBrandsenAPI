<?php


namespace App\AbstractClass;
abstract class CategoriaHijasAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idPadre'=>$this->getCategoriapadreId()->getId(),
            'nombre' => $this->getDescripcion(),
            
           
        ];  
    }
}