<?php


namespace App\AbstractClass;
abstract class EmprendimientoAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            
           
        ];  
    }
}