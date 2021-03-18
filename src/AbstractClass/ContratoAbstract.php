<?php


namespace App\AbstractClass;

abstract class ContratoAbstract
{
    public function getArray()
    {
        return [
            'id' => $this->getId(),
            'desde' => $this->getDesde()->format('Y-m-d H:i:s'),
            'hasta' => $this->getHasta()->format('Y-m-d H:i:s'),
            'paquete' => $this->getPaquete()->getNombre(),
            'cantDestacada' => $this->getCantDestacadas(),
            'cantnormal' => $this->getCantPublicaciones(),
            'pago' => $this->getPago(),
            'userMail'=> $this->getUsuario()->getEmail(),
            'userCel'=> $this->getUsuario()->getTelefono(),
            'userTipo'=> $this->getUsuario()->getTipousuarioId()->getDescripcion(),
        ];
    }

    public function crearContrato(
        $usuario,
        $desde,
        $hasta,
        $paqueteOBJ,
        $cantpublicaciones,
        $cantdestacadas,
        $pago
    ) {
        $this->setUsuario($usuario);
        $this->setDesde($desde);
        $this->setHasta($hasta);
        $this->setPaquete($paqueteOBJ);
        $this->setCantPublicaciones($cantpublicaciones);
        $this->setCantDestacadas($cantdestacadas);
        $this->setPago($pago);
    }
}
