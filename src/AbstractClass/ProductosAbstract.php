<?php


namespace App\AbstractClass;
abstract class ProductosAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            'precio' => $this->getPrecio(),
            
        ];  
    }
    public function crearProducto($nombre,$precio){                  
                $this->setNombre($nombre);         
                $this->setPrecio($precio);               
                
    }

    

     
}
