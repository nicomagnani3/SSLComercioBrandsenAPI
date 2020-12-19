<?php


namespace App\AbstractClass;
abstract class ImagenesPublicacionAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idpublicacion' => $this->getPublicacionId()->getId(),
            'ubicacion'=>$this->getUbicacion()           
        ];  
    }
}