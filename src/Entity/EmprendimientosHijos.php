<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\AbstractClass\EmprendimientosHijosAbstract;
/**
 * @ORM\Entity(repositoryClass="App\Repository\EmprendimientosHijosRepository")
 */
class EmprendimientosHijos extends EmprendimientosHijosAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Emprendimientos", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $emprendimiento;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descripcion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmprendimiento(): ?Emprendimientos
    {
        return $this->emprendimiento;
    }

    public function setEmprendimiento(Emprendimientos $emprendimiento): self
    {
        $this->emprendimiento = $emprendimiento;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}
