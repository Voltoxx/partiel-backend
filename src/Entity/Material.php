<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La quantité ne peut pas être vide.')]
    #[Assert\Positive(message: 'La quantité doit être un nombre positif.')]
    #[Assert\Type(type: 'integer', message: 'La quantité doit être un entier.')]
    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix ne peut pas être vide.')]
    #[Assert\Positive(message: 'Le prix doit être un nombre positif.')]
    #[Assert\Type(type: 'numeric', message: 'Le prix doit être un nombre.')]
    private ?float $prix = null;

    #[ORM\OneToMany(mappedBy: 'material', targetEntity: CommandeMaterial::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $commandeMaterials;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, CommandeMaterial>
     */
    public function getCommandeMateriels(): Collection
    {
        return $this->commandeMaterials;
    }

    public function addCommandeMateriel(CommandeMaterial $commandeMaterial): static
    {
        if (!$this->commandeMaterials->contains($commandeMaterial)) {
            $this->commandeMaterials->add($commandeMaterial);
            $commandeMaterial->setMaterial($this);
        }

        return $this;
    }

    public function removeCommandeMaterial(CommandeMaterial $commandeMaterial): static
    {
        if ($this->commandeMaterials->removeElement($commandeMaterial)) {
            if ($commandeMaterial->getMaterial() === $this) {
                $commandeMaterial->setMaterial(null);
            }
        }

        return $this;
    }
}
