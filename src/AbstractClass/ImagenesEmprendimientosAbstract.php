<?php


namespace App\AbstractClass;
abstract class ImagenesEmprendimientosAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idpublicacion' => $this->getEmprendimientoId()->getId(),
            'ubicacion'=>$this->getUbicacion()           
        ];  
    }
}