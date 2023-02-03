<?php


namespace App\AbstractClass;

abstract class UtilidadesAbstract
{
    public function getArray()
    {

        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'imagen' => $this->getImagen(),
			'imagenprincipal' => $this->getImagenprincipal(),
			'descripcion' => $this->getDescripcion(),

        ];
    }
    public function crearGuiaComercial(
        $nombre,
        $imagen
    ) {
        $this->setNombre($nombre);
        $this->setImagen($imagen);
    }
}
