<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\ClienteAbstract;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClienteRepository")
 */
class Cliente extends ClienteAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuarios;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $apellido;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $DNI;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $celular;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarios(): ?User
    {
        return $this->usuarios;
    }

    public function setUsuarios(User $usuarios): self
    {
        $this->usuarios = $usuarios;

        return $this;
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

    public function getApellido(): ?string
    {
        return $this->apellido;
    }

    public function setApellido(?string $apellido): self
    {
        $this->apellido = $apellido;

        return $this;
    }

    public function getDNI(): ?int
    {
        return $this->DNI;
    }

    public function setDNI(?int $DNI): self
    {
        $this->DNI = $DNI;

        return $this;
    }

    public function getCelular(): ?int
    {
        return $this->celular;
    }

    public function setCelular(?int $celular): self
    {
        $this->celular = $celular;

        return $this;
    }
}
