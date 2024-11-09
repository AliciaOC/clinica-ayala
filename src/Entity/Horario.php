<?php

namespace App\Entity;

use App\Repository\HorarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HorarioRepository::class)]
class Horario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $franja_horaria = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranjaHoraria(): ?string
    {
        return $this->franja_horaria;
    }

    public function setFranjaHoraria(string $franja_horaria): static
    {
        $this->franja_horaria = $franja_horaria;

        return $this;
    }
}
