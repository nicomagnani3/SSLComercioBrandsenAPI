<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\CurriculumAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CurriculumRepository")
 */
class Curriculum extends CurriculumAbstract
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
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $ubicacion;

     /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $tipo;

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
    public function getUbicacion(): ?string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(?string $ubicacion): self
    {
        $this->ubicacion = $ubicacion;

        return $this;
    }
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

}
