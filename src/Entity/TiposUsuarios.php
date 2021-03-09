<?php

namespace App\Entity;

use App\Repository\TiposUsuariosRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\TipoUsuarioAbstract;

/**
 * @ORM\Entity(repositoryClass=TiposUsuariosRepository::class)
 */
class TiposUsuarios extends TipoUsuarioAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descripcion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}
