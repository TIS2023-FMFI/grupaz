<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $vis = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(inversedBy: 'cars')]
    private ?CarGroup $carGroup = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $replacedCar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVis(): ?string
    {
        return $this->vis;
    }

    public function setVis(string $vis): static
    {
        $this->vis = $vis;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCarGroup(): ?CarGroup
    {
        return $this->carGroup;
    }

    public function setCarGroup(?CarGroup $carGroup): static
    {
        $this->carGroup = $carGroup;

        return $this;
    }

    public function getReplacedCar(): ?self
    {
        return $this->replacedCar;
    }

    public function setReplacedCar(?self $replacedCar): static
    {
        $this->replacedCar = $replacedCar;

        return $this;
    }

    public function __toString(): string
    {
        return $this->vis;
    }
}
