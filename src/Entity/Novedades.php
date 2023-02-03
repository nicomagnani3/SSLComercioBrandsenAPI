<?php

namespace App\Entity;

use App\Repository\NovedadesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\NovedadesAbstract;

/**
 * @ORM\Entity(repositoryClass=NovedadesRepository::class)
 */
class Novedades extends NovedadesAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descrpicion;

    /**
     * @ORM\OneToOne(targetEntity=GuiaComercial::class, cascade={"persist", "remove"})
     */
    private $guiacomercialid;

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

    public function getDescrpicion(): ?string
    {
        return $this->descrpicion;
    }

    public function setDescrpicion(?string $descrpicion): self
    {
        $this->descrpicion = $descrpicion;

        return $this;
    }

    public function getGuiacomercialid(): ?GuiaComercial
    {
        return $this->guiacomercialid;
    }

    public function setGuiacomercialid(?GuiaComercial $guiacomercialid): self
    {
        $this->guiacomercialid = $guiacomercialid;

        return $this;
    }
}
