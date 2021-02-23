<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\PublicacionEmprendimientoAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicacionEmprendimientosRepository")
 */
class PublicacionEmprendimientos extends PublicacionEmprendimientoAbstract
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
    private $idusuariId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     */
    private $precio;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Emprendimientos", cascade={"persist", "remove"})
     */
    private $emprendimiento;

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

    public function getIdusuariId(): ?User
    {
        return $this->idusuariId;
    }

    public function setIdusuariId(User $idusuariId): self
    {
        $this->idusuariId = $idusuariId;

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

    public function getEmprendimiento(): ?Emprendimientos
    {
        return $this->emprendimiento;
    }

    public function setEmprendimiento(?Emprendimientos $emprendimiento): self
    {
        $this->emprendimiento = $emprendimiento;

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
