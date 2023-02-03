<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\ImagenesAlquilerAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ImagenesAlquilerRepository")
 */
class ImagenesAlquiler extends ImagenesAlquilerAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Alquiler", cascade={"persist", "remove"})
     */
    private $alquilerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ubicacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlquilerId(): ?Alquiler
    {
        return $this->alquilerId;
    }

    public function setAlquilerId(?Alquiler $alquilerId): self
    {
        $this->alquilerId = $alquilerId;

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
