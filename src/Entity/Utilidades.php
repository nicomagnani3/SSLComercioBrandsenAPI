<?php

namespace App\Entity;

use App\Repository\UtilidadesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\UtilidadesAbstract;

/**
 * @ORM\Entity(repositoryClass=UtilidadesRepository::class)
 */
class Utilidades extends UtilidadesAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen;
	
	/**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $imagenprincipal;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $descripcion;
   

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }
	  public function getImagenprincipal(): ?string
    {
        return $this->imagenprincipal;
    }

    public function setImagenprincipal(?string $imagenprincipal): self
    {
        $this->imagenprincipal = $imagenprincipal;

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
