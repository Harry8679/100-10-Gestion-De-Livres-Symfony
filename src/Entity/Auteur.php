<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuteurRepository::class)]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom = null;

    // ── Côté INVERSE de la relation ManyToMany ──────────────────────────────
    // Un auteur peut avoir écrit plusieurs livres
    // "mappedBy: 'auteurs'" dit que c'est Livre qui "possède" la relation
    #[ORM\ManyToMany(targetEntity: Livre::class, mappedBy: 'auteurs')]
    private Collection $livres;

    public function __construct()
    {
        // ArrayCollection est le tableau Doctrine pour les relations
        $this->livres = new ArrayCollection();
    }

    // ===== GETTERS & SETTERS =====

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getLivres(): Collection { return $this->livres; }
}