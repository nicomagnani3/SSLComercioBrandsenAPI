<?php


namespace App\AbstractClass;
abstract class ClienteAbstract
{
    public function getArray()
    {
   
        return [
            'id'=>$this->getId(),
            'nombre' => $this->getNombre() . ' '.  $this->getApellido(),             
            'usuario'=> $this->getUsuarios()->getId(),
            'email'=> $this->getUsuarios()->getEmail(),
            'tipo'=>$this->getUsuarios()->getGrupos(),
            'tipoPaquete'=> 2,
            'rol'=> $this->getUsuarios()->getTipousuarioId() == NULL ? 'No asignado' :  $this->getUsuarios()->getTipousuarioId()->getNombre(),
           
        ];  
    }
}