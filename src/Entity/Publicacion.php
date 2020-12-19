<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\PublicacionAbstract;
/**

 * @ORM\Entity(repositoryClass="App\Repository\PublicacionRepository")
 */
class Publicacion extends PublicacionAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

  

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @ORM\Column(type="decimal", precision=18, scale=2, nullable=true)
     */
    private $precio;  


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descripcion;   

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="publicaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $IDusuario;

    


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Categorias", cascade={"persist", "remove"})
     */
    private $categoria;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CategoriasHijas", cascade={"persist", "remove"})
     */
    private $categoriaHija;

  

   

    public function __construct()
    {
      
    }

    public function getId(): ?int
    {
        return $this->id;
    }

 
  


    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getEsOferta(): ?bool
    {
        return $this->esOferta;
    }

    public function setEsOferta(?bool $esOferta): self
    {
        $this->esOferta = $esOferta;

        return $this;
    }
  

    public function getdescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setdescripcion(string $string): self
    {
        $this->descripcion = $string;

        return $this;
    }
  
    public function getIDusuario(): ?User
    {
        return $this->IDusuario;
    }

    public function setIDusuario(?User $IDusuario): self
    {
        $this->IDusuario = $IDusuario;

        return $this;
    }

   

    

    public function getCategoria(): ?Categorias
    {
        return $this->categoria;
    }

    public function setCategoria(?Categorias $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getCategoriaHija(): ?CategoriasHijas
    {
        return $this->categoriaHija;
    }

    public function setCategoriaHija(?CategoriasHijas $categoriaHija): self
    {
        $this->categoriaHija = $categoriaHija;

        return $this;
    }

   

  
   
    

  
}