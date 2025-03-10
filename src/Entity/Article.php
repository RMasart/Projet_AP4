<?php
namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'boolean')] 
    private ?bool $disponibilite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)] 
    private ?float $prix = null;

    #[ORM\Column(length: 255, nullable: true)] 
    private ?string $image = null;

    #[ORM\OneToMany(targetEntity: Stocker::class, mappedBy: 'article')]
    private Collection $stockers;

    public function __construct()
    {
        $this->stockers = new ArrayCollection();
    }

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

    public function isDisponibilite(): ?bool 
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getStockers(): Collection
    {
        return $this->stockers;
    }

    public function addStocker(Stocker $stocker): static
    {
        if (!$this->stockers->contains($stocker)) {
            $this->stockers->add($stocker);
            $stocker->setArticle($this);
        }
        return $this;
    }
}
