<?php


namespace App\AbstractClass;
abstract class ImagenesAlquilerAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idpublicacion' => $this->getAlquilerId()->getId(),
            'ubicacion'=>$this->getUbicacion()           
        ];  
    }
}