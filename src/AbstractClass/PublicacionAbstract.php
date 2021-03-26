<?php


namespace App\AbstractClass;
use \Datetime;
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
            'fecha' => $this->getFecha()->format('Y-m-d H:i:s'),
            'precio' => $this->getPrecio(),
            'titulo'=>$this->getTitulo(),
            'descripcion'=>$this->getdescripcion(),
            'destacado'=>$this->getDestacada(),
            'imagen'=>$data,
            'telefono'=>$this->getIDusuario()->getTelefono(),
            'padre'=>$this->getCategoria()->getNombre(),
            'hijo'=>$this->getCategoriaHija()->getDescripcion(),
            'tipo'=>'PRODUCTO',
            'email'=> $this->getIDusuario()->getEmail(),
            'web'=>$this->getIDusuario()->getWeb(),
        ];  
    }
    public function crearPublicacion( $titulo,
    $importe,
    $fecha,
    $observaciones,
    $usuario,
    $categoria,
    $categoriaHija,
    $destacada,
    $pago ,
    $hasta   ){                  
                $this->setIDusuario($usuario);         
                $this->setFecha($fecha);               
                $this->setTitulo($titulo);     
                $this->setPrecio($importe);                
                $this->setCategoria($categoria);
                $this->setCategoriaHija($categoriaHija);  
                $this->setDestacada($destacada);  
                $this->setPago($pago);     
                $this->setHasta($hasta);
                if($observaciones == NULL){  
                    $this->setdescripcion('SN');
                } else{
                     $this->setdescripcion($observaciones);
                    }   

    }

    public function actualizarmespublicacion(){
        $desde = new Datetime();
        $fecha_actual = date("d-m-Y");
        $hasta = date("d-m-Y", strtotime($fecha_actual . "+ 1 month"));
        $hasta = new Datetime($hasta);
        $this->setHasta($hasta);
        $this->setFecha($desde); 
    }

     
}
