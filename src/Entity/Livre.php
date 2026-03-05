<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneePublication = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // ── Côté PROPRIÉTAIRE de la relation ManyToMany ─────────────────────────
    // "inversedBy: 'livres'" dit qu'Auteur a la propriété $livres de l'autre côté
    // Doctrine crée automatiquement une table de jointure : "livre_auteur"
    //   avec les colonnes : livre_id, auteur_id
    #[ORM\ManyToMany(targetEntity: Auteur::class, inversedBy: 'livres')]
    #[ORM\JoinTable(name: 'livre_auteur')]
    private Collection $auteurs;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
    }

    // ===== GETTERS & SETTERS =====

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getAnneePublication(): ?int { return $this->anneePublication; }
    public function setAnneePublication(?int $annee): static { $this->anneePublication = $annee; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    // ── Méthodes pour gérer la collection d'auteurs ─────────────────────────

    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): static
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs->add($auteur);
        }
        return $this;
    }

    public function removeAuteur(Auteur $auteur): static
    {
        $this->auteurs->removeElement($auteur);
        return $this;
    }
}