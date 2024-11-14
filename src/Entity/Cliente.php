<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\OneToOne(inversedBy: 'cliente', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    /**
     * @var Collection<int, Terapeuta>
     */
    #[ORM\ManyToMany(targetEntity: Terapeuta::class, mappedBy: 'clientes')]
    private Collection $terapeutas;

    /**
     * @var Collection<int, Cita>
     */
    #[ORM\OneToMany(targetEntity: Cita::class, mappedBy: 'cliente')]
    private Collection $citas;

    public function __construct()
    {
        $this->terapeutas = new ArrayCollection();
        $this->citas = new ArrayCollection();
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

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(User $usuario): static
    {
        $this->usuario = $usuario;

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
            $terapeuta->addCliente($this);
        }

        return $this;
    }

    public function removeTerapeuta(Terapeuta $terapeuta): static
    {
        if ($this->terapeutas->removeElement($terapeuta)) {
            $terapeuta->removeCliente($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Cita>
     */
    public function getCitas(): Collection
    {
        return $this->citas;
    }

    public function addCita(Cita $cita): static
    {
        if (!$this->citas->contains($cita)) {
            $this->citas->add($cita);
            $cita->setCliente($this);
        }

        return $this;
    }

    public function removeCita(Cita $cita): static
    {
        if ($this->citas->removeElement($cita)) {
            // set the owning side to null (unless already changed)
            if ($cita->getCliente() === $this) {
                $cita->setCliente(null);
            }
        }

        return $this;
    }
}
