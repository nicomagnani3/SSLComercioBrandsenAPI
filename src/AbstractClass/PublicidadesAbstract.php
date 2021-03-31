<?php


namespace App\AbstractClass;

abstract class PublicidadesAbstract
{
    public function getArray()
    {
        return [
            'id' => $this->getId(),
            'imagen' => $this->getImagen(),
            'url' => $this->getUrl(),
            'ubicacion' => $this->getUbicacion(),
        ];
    }
}
