<?php

namespace App\Entity;

use App\Repository\PreciosPublicacionesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\PreciosPublicacionesAbstract;

/**
 * @ORM\Entity(repositoryClass=PreciosPublicacionesRepository::class)
 */
class PreciosPublicaciones extends PreciosPublicacionesAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $precio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }
}
