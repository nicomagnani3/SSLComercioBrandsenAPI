<?php


namespace App\AbstractClass;
abstract class PublicacionAbstract
{
    public function getArray()
    {
        $ubicacion='imagenes/'.$this->getId().'-0.png';     
        $img = file_get_contents( 
            $ubicacion); 
            $data = base64_encode($img); 
        return [
            'id'=>$this->getId(),
            'fecha' => $this->getFecha(),
            'precio' => $this->getPrecio(),
            'titulo'=>$this->getTitulo(),
            'descripcion'=>$this->getdescripcion(),
            'imagen'=>$data
           
        ];  
    }
    public function crearPublicacion( $titulo,
    $importe,
    $fecha,
    $observaciones,
    $usuario,
    $categoria,
    $categoriaHija      ){                  
                $this->setIDusuario($usuario);         
                $this->setFecha($fecha);               
                $this->setTitulo($titulo);     
                $this->setPrecio($importe);                
                $this->setCategoria($categoria);
                $this->setCategoriaHija($categoriaHija);                   
                if($observaciones == NULL){  
                    $this->setdescripcion('SN');
                } else{
                     $this->setdescripcion($observaciones);
                    }   

    }

    

     
}
