<?php

namespace App\Entity;

use App\Repository\RubrosRepository;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\RubrosAbstract;

/**
 * @ORM\Entity(repositoryClass=RubrosRepository::class)
 */
class Rubros extends  RubrosAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nombre;

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
}
