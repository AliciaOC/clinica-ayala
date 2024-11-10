<?php

namespace App\Entity;

use App\Repository\HorarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Terapeuta>
     */
    #[ORM\ManyToMany(targetEntity: Terapeuta::class, mappedBy: 'horario')]
    private Collection $terapeutas;

    public function __construct()
    {
        $this->terapeutas = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Terapeuta>
     */
    public function getTerapeutas(): Collection
    {
        return $this->terapeutas;
    }

    public function addTerapeuta(Terapeuta $terapeuta): static
    {
        if (!$this->terapeutas->contains($terapeuta)) {
            $this->terapeutas->add($terapeuta);
            $terapeuta->addHorario($this);
        }

        return $this;
    }

    public function removeTerapeuta(Terapeuta $terapeuta): static
    {
        if ($this->terapeutas->removeElement($terapeuta)) {
            $terapeuta->removeHorario($this);
        }

        return $this;
    }
}
