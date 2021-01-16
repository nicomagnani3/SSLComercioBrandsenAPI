<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImagenesServiciosRepository")
 */
class ImagenesServicios
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PublicacionServicios", cascade={"persist", "remove"})
     */
    private $serviciosId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ubicacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiciosId(): ?PublicacionServicios
    {
        return $this->serviciosId;
    }

    public function setServiciosId(?PublicacionServicios $serviciosId): self
    {
        $this->serviciosId = $serviciosId;

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
