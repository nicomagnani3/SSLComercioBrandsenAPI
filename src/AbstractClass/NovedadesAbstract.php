<?php


namespace App\AbstractClass;

abstract class NovedadesAbstract
{
    public function getArray()
    {

        return [
            'id' => $this->getId(),
            'nombre' => $this->getNombre(),
            'imagen' => $this->getImagen(),
            'descripcion' => $this->getDescrpicion(),
            'guia' => $this->getGuiacomercialid(),
          
        ];
    }
    
    public function crearNovedad( 
    $nombre,
    $imagen,
     $observaciones,
    $empresaOBJ 
      ){                  
                $this->setNombre($nombre);         
                $this->setImagen($imagen);               
                $this->setDescrpicion($observaciones);     
                $this->setGuiacomercialid($empresaOBJ);   
            
                             
             

    }
}
