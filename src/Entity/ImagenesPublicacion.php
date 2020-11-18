<?php

namespace App\Entity;
use App\AbstractClass\ImagenesPublicacionAbstract;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Publicacion", inversedBy="imagenesPublicacion")
     */
    private $idpublicacion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipoarchivo;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $archivo;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $contenttype;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $filename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdpublicacion(): ?Publicacion
    {
        return $this->idpublicacion;
    }

    public function setIdpublicacion(?Publicacion $idpublicacion): self
    {
        $this->idpublicacion = $idpublicacion;

        return $this;
    }

    public function getTipoarchivo(): ?int
    {
        return $this->tipoarchivo;
    }

    public function setTipoarchivo(?int $tipoarchivo): self
    {
        $this->tipoarchivo = $tipoarchivo;

        return $this;
    }

    public function getArchivo()
    {
        return $this->archivo;
    }

    public function setArchivo($archivo): self
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getContenttype(): ?string
    {
        return $this->contenttype;
    }

    public function setContenttype(?string $contenttype): self
    {
        $this->contenttype = $contenttype;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
