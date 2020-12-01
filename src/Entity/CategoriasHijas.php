<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\CategoriaHijasAbstract;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoriasHijasRepository")
 */
class CategoriasHijas extends CategoriaHijasAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Categorias", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoriapadreId;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $descripcion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoriapadreId(): ?Categorias
    {
        return $this->categoriapadreId;
    }

    public function setCategoriapadreId(Categorias $categoriapadreId): self
    {
        $this->categoriapadreId = $categoriapadreId;

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
