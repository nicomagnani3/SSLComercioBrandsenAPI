<?php


namespace App\AbstractClass;
abstract class PublicacionServicioAbstract
{
    public function getArray()
    {
        $ubicacion='imagenesServicios/'.$this->getId().'-0.png';     
        $img = file_get_contents( 
            $ubicacion); 
            $data = base64_encode($img); 
        return [
            'id'=>$this->getId(),
            'fecha' => $this->getFecha(),
            'precio' => $this->getPrecio(),
            'titulo'=>$this->getTitulo(),
            'descripcion'=>$this->getdescripcion(),
            'imagen'=>$data,
            'destacado'=>$this->getDestacada()           
        ];  
    }
    public function crearPublicacion(
    $titulo,
    $importe,
    $fecha,
    $observaciones,
    $usuario,
    $servicioOBJ,
    $servicioHijoOBJ,
    $destacada
        ){                  
                $this->setIdusuario($usuario);         
                $this->setFecha($fecha);               
                $this->setTitulo($titulo);     
                $this->setPrecio($importe);                
                $this->setServicioId($servicioOBJ);
                $this->setServiciohijoId($servicioHijoOBJ);                
                $this->setDestacada($destacada);                
                if($observaciones == NULL){  
                    $this->setDescripcion('SN');
                } else{
                     $this->setDescripcion($observaciones);
                    }   

    }

    

     
}