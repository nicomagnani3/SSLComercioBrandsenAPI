<?php


namespace App\AbstractClass;
abstract class ServiciosHijosAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idPadre'=>$this->getServicio()->getId(),
            'nombre' => $this->getDescripcion(),
            
           
        ];  
    }
}