<?php

namespace App\Entity;

use App\Repository\TerapeutaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToOne(inversedBy: 'terapeuta', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    /**
     * @var Collection<int, Tratamiento>
     */
    #[ORM\ManyToMany(targetEntity: Tratamiento::class, mappedBy: 'terapeutas')]
    private Collection $tratamientos;

    /**
     * @var Collection<int, Cliente>
     */
    #[ORM\ManyToMany(targetEntity: Cliente::class, inversedBy: 'terapeutas')]
    private Collection $clientes;

    /**
     * @var Collection<int, Horario>
     */
    #[ORM\ManyToMany(targetEntity: Horario::class, inversedBy: 'terapeutas')]
    private Collection $horario;

    /**
     * @var Collection<int, Cita>
     */
    #[ORM\OneToMany(targetEntity: Cita::class, mappedBy: 'terapeuta', orphanRemoval: true)]
    private Collection $citas;

    public function __construct()
    {
        $this->tratamientos = new ArrayCollection();
        $this->clientes = new ArrayCollection();
        $this->horario = new ArrayCollection();
        $this->citas = new ArrayCollection();
    }

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
     * @return Collection<int, Tratamiento>
     */
    public function getTratamientos(): Collection
    {
        return $this->tratamientos;
    }

    public function addTratamiento(Tratamiento $tratamiento): static
    {
        if (!$this->tratamientos->contains($tratamiento)) {
            $this->tratamientos->add($tratamiento);
            $tratamiento->addTerapeuta($this);
        }

        return $this;
    }

    public function removeTratamiento(Tratamiento $tratamiento): static
    {
        if ($this->tratamientos->removeElement($tratamiento)) {
            $tratamiento->removeTerapeuta($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Cliente>
     */
    public function getClientes(): Collection
    {
        return $this->clientes;
    }

    public function addCliente(Cliente $cliente): static
    {
        if (!$this->clientes->contains($cliente)) {
            $this->clientes->add($cliente);
        }

        return $this;
    }

    public function removeCliente(Cliente $cliente): static
    {
        $this->clientes->removeElement($cliente);

        return $this;
    }

    /**
     * @return Collection<int, Horario>
     */
    public function getHorario(): Collection
    {
        return $this->horario;
    }

    public function addHorario(Horario $horario): static
    {
        if (!$this->horario->contains($horario)) {
            $this->horario->add($horario);
        }

        return $this;
    }

    public function removeHorario(Horario $horario): static
    {
        $this->horario->removeElement($horario);

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
            $cita->setTerapeuta($this);
        }

        return $this;
    }

    public function removeCita(Cita $cita): static
    {
        if ($this->citas->removeElement($cita)) {
            // set the owning side to null (unless already changed)
            if ($cita->getTerapeuta() === $this) {
                $cita->setTerapeuta(null);
            }
        }

        return $this;
    }
}
