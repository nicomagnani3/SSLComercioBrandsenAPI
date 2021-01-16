<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImagenesEmprendimientosRepository")
 */
class ImagenesEmprendimientos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PublicacionEmprendimientos", cascade={"persist", "remove"})
     */
    private $emprendimientoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ubicacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmprendimientoId(): ?PublicacionEmprendimientos
    {
        return $this->emprendimientoId;
    }

    public function setEmprendimientoId(?PublicacionEmprendimientos $emprendimientoId): self
    {
        $this->emprendimientoId = $emprendimientoId;

        return $this;
    }

    public function getUbicacion(): ?string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?string $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }
}
