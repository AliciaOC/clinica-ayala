<?php

namespace App\Entity;

use App\Repository\TratamientoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TratamientoRepository::class)]
class Tratamiento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descripcion = null;

    /**
     * @var Collection<int, Terapeuta>
     */
    #[ORM\ManyToMany(targetEntity: Terapeuta::class, inversedBy: 'tratamientos')]
    private Collection $terapeuta;

    public function __construct()
    {
        $this->terapeuta = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Terapeuta>
     */
    public function getTerapeuta(): Collection
    {
        return $this->terapeuta;
    }

    public function addTerapeuta(Terapeuta $terapeuta): static
    {
        if (!$this->terapeuta->contains($terapeuta)) {
            $this->terapeuta->add($terapeuta);
        }

        return $this;
    }

    public function removeTerapeuta(Terapeuta $terapeuta): static
    {
        $this->terapeuta->removeElement($terapeuta);

        return $this;
    }
}
