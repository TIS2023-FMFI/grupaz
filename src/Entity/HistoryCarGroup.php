<?php

namespace App\Entity;

use App\Repository\HistoryCarGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryCarGroupRepository::class)]
class HistoryCarGroup
{
    public const STATUS_FREE = 0;
    public const STATUS_START = 1;
    public const STATUS_SCANNING = 2;
    public const STATUS_ALL_SCANNED = 3;
    public const STATUS_APPROVED = 4;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $gid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frontLicensePlate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backLicensePlate = null;

    #[ORM\OneToMany(mappedBy: 'historyCarGroup', targetEntity: HistoryCar::class)]
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

    public function setFrontLicensePlate(?string $frontLicensePlate): static
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

    public function addCar(HistoryCar $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setHistoryCarGroup($this);
        }

        return $this;
    }

    public function removeCar(HistoryCar $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getHistoryCarGroup() === $this) {
                $car->setHistoryCarGroup(null);
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

    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    public function setReceiver(?string $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }
    public function __toString(): string
    {
        return "CarGroup ".$this->gid;
    }
    public static function translateStatus(?int $cons): string
    {
        return match ($cons) {
            self::STATUS_FREE => 'Voľná',
            self::STATUS_START => 'Prihlásená',
            self::STATUS_SCANNING => 'Skenovanie',
            self::STATUS_ALL_SCANNED => 'Naskenovaná',
            self::STATUS_APPROVED => 'Schválená',
            default => 'nenastavené',
        };
    }
}
