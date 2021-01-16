<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\ServiciosHijosAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiciosHijosRepository")
 */ 
class ServiciosHijos extends ServiciosHijosAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Servicios", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $servicio;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descripcion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServicio(): ?Servicios
    {
        return $this->servicio;
    }

    public function setServicio(Servicios $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}
