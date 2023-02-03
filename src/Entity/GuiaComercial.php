<?php

namespace App\Entity;

use App\Repository\GuiaComercialRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\GuiaComercialAbstract;

/**
 * @ORM\Entity(repositoryClass=GuiaComercialRepository::class)
 */
class GuiaComercial extends GuiaComercialAbstract
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToOne(targetEntity=Empresa::class, cascade={"persist", "remove"})
     */
    private $idempresa;

    /**
     * @ORM\OneToOne(targetEntity=Rubros::class, cascade={"persist", "remove"})
     */
    private $rubroid;

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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getIdempresa(): ?Empresa
    {
        return $this->idempresa;
    }

    public function setIdempresa(?Empresa $idempresa): self
    {
        $this->idempresa = $idempresa;

        return $this;
    }

    public function getRubroid(): ?Rubros
    {
        return $this->rubroid;
    }

    public function setRubroid(?Rubros $rubroid): self
    {
        $this->rubroid = $rubroid;

        return $this;
    }
}
