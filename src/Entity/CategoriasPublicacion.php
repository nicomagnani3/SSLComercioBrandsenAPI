<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoriasPublicacionRepository")
 */
class CategoriasPublicacion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Publicacion", inversedBy="categoriaspublicacion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $IDPublicacion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categoria", inversedBy="caregoriaspublicacion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $IDCategoria;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIDPublicacion(): ?Publicacion
    {
        return $this->IDPublicacion;
    }

    public function setIDPublicacion(?Publicacion $IDPublicacion): self
    {
        $this->IDPublicacion = $IDPublicacion;

        return $this;
    }

    public function getIDCategoria(): ?Categoria
    {
        return $this->IDCategoria;
    }

    public function setIDCategoria(?Categoria $IDCategoria): self
    {
        $this->IDCategoria = $IDCategoria;

        return $this;
    }
}
