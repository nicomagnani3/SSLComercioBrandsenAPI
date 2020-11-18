<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\CategoriaAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoriaRepository")
 */
class Categoria extends CategoriaAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoriasPublicacion", mappedBy="IDCategoria")
     */
    private $caregoriaspublicacion;

    public function __construct()
    {
        $this->caregoriaspublicacion = new ArrayCollection();
    }

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCategoriasPublicacion(): ?CategoriasPublicacion
    {
        return $this->categoriasPublicacion;
    }

    public function setCategoriasPublicacion(?CategoriasPublicacion $categoriasPublicacion): self
    {
        $this->categoriasPublicacion = $categoriasPublicacion;

        return $this;
    }

    /**
     * @return Collection|CategoriasPublicacion[]
     */
    public function getCaregoriaspublicacion(): Collection
    {
        return $this->caregoriaspublicacion;
    }

    public function addCaregoriaspublicacion(CategoriasPublicacion $caregoriaspublicacion): self
    {
        if (!$this->caregoriaspublicacion->contains($caregoriaspublicacion)) {
            $this->caregoriaspublicacion[] = $caregoriaspublicacion;
            $caregoriaspublicacion->setIDCategoria($this);
        }

        return $this;
    }

    public function removeCaregoriaspublicacion(CategoriasPublicacion $caregoriaspublicacion): self
    {
        if ($this->caregoriaspublicacion->contains($caregoriaspublicacion)) {
            $this->caregoriaspublicacion->removeElement($caregoriaspublicacion);
            // set the owning side to null (unless already changed)
            if ($caregoriaspublicacion->getIDCategoria() === $this) {
                $caregoriaspublicacion->setIDCategoria(null);
            }
        }

        return $this;
    }
}
