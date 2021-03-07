<?php


namespace App\AbstractClass;
abstract class ImagenesServiciosAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idpublicacion' => $this->getServiciosId()->getId(),
            'ubicacion'=>$this->getUbicacion()           
        ];  
    }
}