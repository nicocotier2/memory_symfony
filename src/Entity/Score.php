<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
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
    private ?int $score = null;

    #[ORM\Column]
    private ?int $error = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $played_on = null;

    #[ORM\ManyToOne(inversedBy: 'user_score')]
    private ?user $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

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

    public function getPlayedOn(): ?\DateTimeInterface
    {
        return $this->played_on;
    }

    public function setPlayedOn(\DateTimeInterface $played_on): static
    {
        $this->played_on = $played_on;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }
}
