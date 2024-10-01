<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $temps = null;

    #[ORM\Column]
    private ?int $error = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $game_date = null;

    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToOne(targetEntity: user::class, mappedBy: 'score_user')]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemps(): ?int
    {
        return $this->temps;
    }

    public function setTemps(int $temps): static
    {
        $this->temps = $temps;

        return $this;
    }

    public function getError(): ?int
    {
        return $this->error;
    }

    public function setError(int $error): static
    {
        $this->error = $error;

        return $this;
    }

    public function getGameDate(): ?\DateTimeInterface
    {
        return $this->game_date;
    }

    public function setGameDate(\DateTimeInterface $game_date): static
    {
        $this->game_date = $game_date;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addIdUser(user $User): static
    {
        if (!$this->id_user->contains($User)) {
            $this->user->add($User);
            $User->setScoreUser($this);
        }

        return $this;
    }

    public function removeIdUser(user $User): static
    {
        if ($this->user->removeElement($User)) {
            // set the owning side to null (unless already changed)
            if ($User->getScoreUser() === $this) {
                $User->setScoreUser(null);
            }
        }

        return $this;
    }
}
