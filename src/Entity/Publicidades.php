<?php

namespace App\Entity;

use App\Repository\PublicidadesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\PublicidadesAbstract;

/**
 * @ORM\Entity(repositoryClass=PublicidadesRepository::class)
 */
class Publicidades  extends PublicidadesAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imagen;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     */
    private $ubicacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUbicacion(): ?int
    {
        return $this->ubicacion;
    }

    public function setUbicacion(int $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }
}
