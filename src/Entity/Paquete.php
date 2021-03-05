<?php

namespace App\Entity;

use App\Repository\PaqueteRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\PaqueteAbstract;

/**
 * @ORM\Entity(repositoryClass=PaqueteRepository::class)
 */
class Paquete extends PaqueteAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantNormal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantDestacada;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $precio;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipo;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantNormal(): ?int
    {
        return $this->cantNormal;
    }

    public function setCantNormal(?int $cantNormal): self
    {
        $this->cantNormal = $cantNormal;

        return $this;
    }

    public function getCantDestacada(): ?int
    {
        return $this->cantDestacada;
    }

    public function setCantDestacada(?int $cantDestacada): self
    {
        $this->cantDestacada = $cantDestacada;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(?string $precio): self
    {
        $this->precio = $precio;

        return $this;
    }
    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function setTipo(?int $cantDestacada): self
    {
        $this->tipo = $cantDestacada;

        return $this;
    }
}
