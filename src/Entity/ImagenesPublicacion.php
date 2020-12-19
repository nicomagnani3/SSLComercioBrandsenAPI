<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\ImagenesPublicacionAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ImagenesPublicacionRepository")
 */
class ImagenesPublicacion extends ImagenesPublicacionAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Publicacion", cascade={"persist", "remove"})
     */
    private $publicacionId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ubicacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicacionId(): ?Publicacion
    {
        return $this->publicacionId;
    }

    public function setPublicacionId(?Publicacion $publicacionId): self
    {
        $this->publicacionId = $publicacionId;

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
