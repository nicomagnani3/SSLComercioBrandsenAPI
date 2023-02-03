<?php


namespace App\AbstractClass;

abstract class GuiaComercialAbstract
{
    public function getArray()
    {

        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'imagen' => $this->getImagen(),
            'descripcion' => $this->getDescripcion(),
            'empresa' => $this->getIdempresa(),
            'usuario' => $this->getIdempresa() == null ? NULL : $this->getIdempresa()->getUsuarios()->getId(),
            'rubroId'=> $this->getRubroid() == null ? NULL : $this->getRubroid()->getId(),
            'rubroNombre'=> $this->getRubroid() == null ? NULL : $this->getRubroid()->getNombre()
        ];
    }
    
    public function crearGuiaComercial( 
    $nombre,
    $imagen,
     $observaciones,
    $empresaOBJ ,
    $rubroOBJ  ){                  
                $this->setNombre($nombre);         
                $this->setImagen($imagen);               
                $this->setDescripcion($observaciones);     
                $this->setIdempresa($empresaOBJ);   
                $this->setRubroid($rubroOBJ);  
                             
             

    }
}
