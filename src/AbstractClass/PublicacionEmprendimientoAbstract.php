<?php


namespace App\AbstractClass;
use \Datetime;

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
            'telefono'=>$this->getIdusuariId()->getTelefono(),
            'padre'=>$this->getEmprendimiento()->getNombre(),
            'tipo'=>'EMPRENDIMIENTO',
            'email'=> $this->getIdusuariId()->getEmail(),
			'web'=>$this->getIdusuariId()->getWeb(),
        ];  
    }
    public function crearPublicacion( $titulo,
    $importe,
    $fecha,
    $observaciones,
    $usuario,
    $emprendimiento,
    $destacada,
    $hasta
        ){                  
                $this->setIdusuariId($usuario);         
                $this->setFecha($fecha);               
                $this->setTitulo($titulo);     
                $this->setPrecio($importe);                
                $this->setEmprendimiento($emprendimiento);
                $this->setDestacada($destacada);
                $this->setPago(1);
                $this->setHasta($hasta);
                if($observaciones == NULL){  
                    $this->setDescripcion('SN');
                } else{
                     $this->setDescripcion($observaciones);
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
