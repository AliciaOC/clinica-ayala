<?php

namespace App\Entity;

use App\Repository\TerapeutaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerapeutaRepository::class)]
class Terapeuta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulacion = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $horario = null;

    #[ORM\OneToOne(inversedBy: 'terapeuta', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulacion(): ?string
    {
        return $this->titulacion;
    }

    public function setTitulacion(string $titulacion): static
    {
        $this->titulacion = $titulacion;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getHorario(): ?string
    {
        return $this->horario;
    }

    public function setHorario(string $horario): static
    {
        $this->horario = $horario;

        return $this;
    }

    public function getIdUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setIdUsuario(User $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }
}
