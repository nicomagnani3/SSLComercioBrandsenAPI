<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmpresaRepository")
 */
class Empresa
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nombre;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuarios;

    /**
     * @ORM\OneToOne(targetEntity=Rubros::class, cascade={"persist", "remove"})
     */
    private $rubroId;

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

  
    public function getUsuarios(): ?User
    {
        return $this->usuarios;
    }

    public function setUsuarios(User $usuarios): self
    {
        $this->usuarios = $usuarios;

        return $this;
    }

    public function getRubroId(): ?Rubros
    {
        return $this->rubroId;
    }

    public function setRubroId(?Rubros $rubroId): self
    {
        $this->rubroId = $rubroId;

        return $this;
    }
}
