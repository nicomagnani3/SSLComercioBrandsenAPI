<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\PublicacionServicioAbstract;

use \Datetime;
/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicacionServiciosRepository")
 */
class PublicacionServicios extends PublicacionServicioAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $idusuario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;
    /**
     * @ORM\Column(type="datetime")
     */
    private $hasta;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $precio;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Servicios", cascade={"persist", "remove"})
     */
    private $servicioId;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ServiciosHijos", cascade={"persist", "remove"})
     */
    private $serviciohijoId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $destacada;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pago;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdusuario(): ?User
    {
        return $this->idusuario;
    }

    public function setIdusuario(User $idusuario): self
    {
        $this->idusuario = $idusuario;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
    public function getHasta(): ?\DateTimeInterface
    {
        return $this->hasta;
    }

    public function setHasta(\DateTimeInterface $hasta): self
    {
        $this->hasta = $hasta;

        return $this;
    }
    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

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

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getServicioId(): ?Servicios
    {
        return $this->servicioId;
    }

    public function setServicioId(?Servicios $servicioId): self
    {
        $this->servicioId = $servicioId;

        return $this;
    }

    public function getServiciohijoId(): ?ServiciosHijos
    {
        return $this->serviciohijoId;
    }

    public function setServiciohijoId(?ServiciosHijos $serviciohijoId): self
    {
        $this->serviciohijoId = $serviciohijoId;

        return $this;
    }

    public function getDestacada(): ?bool
    {
        return $this->destacada;
    }

    public function setDestacada(?bool $destacada): self
    {
        $this->destacada = $destacada;

        return $this;
    }
    public function getPago(): ?bool
    {
        return $this->pago;
    }

    public function setPago(?bool $pago): self
    {
        $this->pago = $pago;

        return $this;
    }

}
