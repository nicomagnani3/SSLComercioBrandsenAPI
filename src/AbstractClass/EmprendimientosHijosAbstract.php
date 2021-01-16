<?php


namespace App\AbstractClass;
abstract class EmprendimientosHijosAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idPadre'=>$this->getEmprendimiento()->getId(),
            'nombre' => $this->getDescripcion(),
            
           
        ];  
    }
}