<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\AlquilerAbstract;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlquilerRepository")
 */
class Alquiler extends AlquilerAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id; 
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $propiedad;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $operacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="publicaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $IDusuario;
   
     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coordenadas;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getPropiedad(): ?string
    {
        return $this->propiedad;
    }

    public function setPropiedad(string $propiedad): self
    {
        $this->propiedad = $propiedad;

        return $this;

    }
    public function getOperacion():?string
    {
        return $this->operacion;
    }

    public function setOperacion(string $operacion): self
    {
        $this->operacion = $operacion;

        return $this;
    }


    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(string $string): self
    {
        $this->observaciones = $string;

        return $this;
    }
    public function getCoordenadas(): ?string
    {
        return $this->coordenadas;
    }

    public function setCoordenadas(string $string): self
    {
        $this->coordenadas = $string;

        return $this;
    }

    public function getIDusuario(): ?User
    {
        return $this->IDusuario;
    }

    public function setIDusuario(?User $IDusuario): self
    {
        $this->IDusuario = $IDusuario;

        return $this;
    }
}
