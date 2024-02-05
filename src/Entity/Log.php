<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $time = null;

    #[ORM\Column(length: 255)]
    private ?string $log = null;

    #[ORM\Column]
    private ?int $admin_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $object_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 25)]
    private ?string $object_class = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(\DateTimeImmutable $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }

    public function setLog(string $log): static
    {
        $this->log = $log;

        return $this;
    }

    public function getAdminId(): ?int
    {
        return $this->admin_id;
    }

    public function setAdminId(?int $admin_id): void
    {
        $this->admin_id = $admin_id;
    }

    public function getObjectId(): ?int
    {
        return $this->object_id;
    }

    public function setObjectId(?int $object_id): void
    {
        $this->object_id = $object_id;
    }

    public function getObjectClass(): ?string
    {
        return $this->object_class;
    }

    public function setObjectClass(?string $object_class): void
    {
        $this->object_class = $object_class;
    }
}
