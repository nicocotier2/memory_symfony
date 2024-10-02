<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_theme = null;

    /**
     * @var Collection<int, Carte>
     */
    #[ORM\OneToMany(targetEntity: Carte::class, mappedBy: 'theme', orphanRemoval: true)]
    private Collection $theme_carte;

    public function __construct()
    {
        $this->theme_carte = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomTheme(): ?string
    {
        return $this->nom_theme;
    }

    public function setNomTheme(string $nom_theme): static
    {
        $this->nom_theme = $nom_theme;

        return $this;
    }

    /**
     * @return Collection<int, Carte>
     */
    public function getThemeCarte(): Collection
    {
        return $this->theme_carte;
    }

    public function addThemeCarte(Carte $themeCarte): static
    {
        if (!$this->theme_carte->contains($themeCarte)) {
            $this->theme_carte->add($themeCarte);
            $themeCarte->setTheme($this);
        }

        return $this;
    }

    public function removeThemeCarte(Carte $themeCarte): static
    {
        if ($this->theme_carte->removeElement($themeCarte)) {
            // set the owning side to null (unless already changed)
            if ($themeCarte->getTheme() === $this) {
                $themeCarte->setTheme(null);
            }
        }

        return $this;
    }
}
