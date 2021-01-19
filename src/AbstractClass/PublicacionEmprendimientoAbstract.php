<?php


namespace App\AbstractClass;
abstract class PublicacionEmprendimientoAbstract
{
    public function getArray()
    {
        $ubicacion='imagenesEmprendimientos/'.$this->getId().'-0.png';     
        $img = file_get_contents( 
            $ubicacion); 
            $data = base64_encode($img); 
        return [
            'id'=>$this->getId(),
            'fecha' => $this->getFecha()->format('Y-m-d H:i:s'),
            'precio' => $this->getPrecio(),
            'titulo'=>$this->getTitulo(),
            'descripcion'=>$this->getdescripcion(),
            'imagen'=>$data,
            'destacado'=>$this->getDestacada(),
            'telefono'=>$this->getIdusuariId()->getTelefono()
           
        ];  
    }
    public function crearPublicacion( $titulo,
    $importe,
    $fecha,
    $observaciones,
    $usuario,
    $emprendimiento,
    $destacada
        ){                  
                $this->setIdusuariId($usuario);         
                $this->setFecha($fecha);               
                $this->setTitulo($titulo);     
                $this->setPrecio($importe);                
                $this->setEmprendimiento($emprendimiento);
                $this->setDestacada($destacada);
                
                if($observaciones == NULL){  
                    $this->setDescripcion('SN');
                } else{
                     $this->setDescripcion($observaciones);
                    }   

    }

    

     
}
