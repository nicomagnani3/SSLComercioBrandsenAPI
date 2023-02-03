<?php


namespace App\AbstractClass;
abstract class CurriculumAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre(),
            'ubicacion' => $this->getUbicacion(),   
			'tipo' => $this->getTipo(), 			
        ];  
    }
	
	public function crearPublicacion($nombre,$tipo){
		$this->setNombre($nombre);         
        $this->setTipo($tipo);   
	}
}