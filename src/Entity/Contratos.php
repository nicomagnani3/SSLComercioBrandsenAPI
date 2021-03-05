<?php

namespace App\Entity;

use App\Repository\ContratosRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\ContratoAbstract;

/**
 * @ORM\Entity(repositoryClass=ContratosRepository::class)
 */
class Contratos extends ContratoAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $usuario;

       /**
     * @ORM\Column(type="datetime")
     */
    private $desde;

       /**
     * @ORM\Column(type="datetime")
     */
    private $hasta;

    /**
     * @ORM\OneToOne(targetEntity=Paquete::class, cascade={"persist", "remove"})
     */
    private $paquete;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantPublicaciones;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantDestacadas;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pago;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getDesde(): ?\DateTimeInterface
    {
        return $this->desde;
    }

    public function setDesde(?\DateTimeInterface $desde): self
    {
        $this->desde = $desde;

        return $this;
    }

    public function getHasta(): ?\DateTimeInterface
    {
        return $this->hasta;
    }

    public function setHasta(?\DateTimeInterface $hasta): self
    {
        $this->hasta = $hasta;

        return $this;
    }

    public function getPaquete(): ?Paquete
    {
        return $this->paquete;
    }

    public function setPaquete(?Paquete $paquete): self
    {
        $this->paquete = $paquete;

        return $this;
    }

    public function getCantPublicaciones(): ?int
    {
        return $this->cantPublicaciones;
    }

    public function setCantPublicaciones(?int $cantPublicaciones): self
    {
        $this->cantPublicaciones = $cantPublicaciones;

        return $this;
    }

    public function getCantDestacadas(): ?int
    {
        return $this->cantDestacadas;
    }

    public function setCantDestacadas(?int $cantDestacadas): self
    {
        $this->cantDestacadas = $cantDestacadas;

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
