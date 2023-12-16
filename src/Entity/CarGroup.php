<?php

namespace App\Entity;

use App\Repository\CarGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarGroupRepository::class)]
class CarGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 3)]
    private ?string $gid = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\NotBlank(groups: ['worker_form'])]
    #[Assert\Length(max: 10, groups: ['worker_form'])]
    private ?string $frontLicensePlate = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\NotBlank(groups: ['worker_form'])]
    #[Assert\Length(max: 10, groups: ['worker_form'])]
    private ?string $backLicensePlate = null;

    #[ORM\OneToMany(mappedBy: 'carGroup', targetEntity: Car::class)]
    private Collection $cars;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $importTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $exportTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $receiver = null;

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGid(): ?string
    {
        return $this->gid;
    }

    public function setGid(string $gid): static
    {
        $this->gid = $gid;

        return $this;
    }

    public function getFrontLicensePlate(): ?string
    {
        return $this->frontLicensePlate;
    }

    public function setFrontLicensePlate(string $frontLicensePlate): static
    {
        $this->frontLicensePlate = $frontLicensePlate;

        return $this;
    }

    public function getBackLicensePlate(): ?string
    {
        return $this->backLicensePlate;
    }

    public function setBackLicensePlate(?string $backLicensePlate): static
    {
        $this->backLicensePlate = $backLicensePlate;

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setCarGroup($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getCarGroup() === $this) {
                $car->setCarGroup(null);
            }
        }

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

    public function getImportTime(): ?\DateTimeInterface
    {
        return $this->importTime;
    }

    public function setImportTime(\DateTimeInterface $importTime): static
    {
        $this->importTime = $importTime;

        return $this;
    }

    public function getExportTime(): ?\DateTimeInterface
    {
        return $this->exportTime;
    }

    public function setExportTime(?\DateTimeInterface $exportTime): static
    {
        $this->exportTime = $exportTime;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }
    public function __toString(): string
    {
        return "CarGroup ".$this->gid;
    }

    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    public function setReceiver(?string $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }
}
