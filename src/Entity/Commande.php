<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomClient = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: CommandeMaterial::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $commandeMaterials;

    public function __construct()
    {
        $this->commandeMaterials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): static
    {
        $this->nomClient = $nomClient;

        return $this;
    }
    
    public function getCommandeMaterials(): Collection
    {
        return $this->commandeMaterials;
    }

    public function addCommandeMaterial(CommandeMaterial $commandeMaterial): self
    {
        if (!$this->commandeMaterials->contains($commandeMaterial)) {
            $this->commandeMaterials[] = $commandeMaterial;
            $commandeMaterial->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeMaterial(CommandeMaterial $commandeMaterial): self
    {
        if ($this->commandeMaterials->removeElement($commandeMaterial)) {
            // set the owning side to null (unless already changed)
            if ($commandeMaterial->getCommande() === $this) {
                $commandeMaterial->setCommande(null);
            }
        }

        return $this;
    }

    public function getPrixTotal(): float
    {
        $totalPrice = 0;

        foreach ($this->commandeMaterials as $commandeMateriel) {
            $totalPrice += $commandeMateriel->getMaterial()->getPrix() * $commandeMateriel->getQuantite();
        }

        return $totalPrice;
    }
}
