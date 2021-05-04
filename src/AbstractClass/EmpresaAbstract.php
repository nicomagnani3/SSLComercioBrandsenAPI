<?php


namespace App\AbstractClass;

abstract class EmpresaAbstract
{
    public function getArray()
    {

        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'usuario' => $this->getUsuarios()->getId(),
            'email' => $this->getUsuarios()->getEmail(),
            'tipoPaquete' => 1,
            'rol' => $this->getUsuarios()->getTipousuarioId() == NULL ? 'No asignado' :  $this->getUsuarios()->getTipousuarioId()->getNombre(),

        ];
    }
}
