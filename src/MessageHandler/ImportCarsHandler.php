<?php

namespace App\MessageHandler;

use App\Entity\Car;
use App\Entity\CarGroup;
use App\Message\ImportCars;
use App\Message\ImportCar;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\TraceableMessageBus;
use function Symfony\Component\Clock\now;

#[AsMessageHandler]
class ImportCarsHandler
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(ImportCars $importCars): void
    {
        $count = 0;
        $cars = $importCars->getCars();
        foreach ($cars as $key => $car) {
            if ($count > 99) {
                break;
            }
            $this->messageBus->dispatch(new ImportCar($car[0], $car[1], $car[2]));
            unset($cars[$key]);
            $count++;
        }
        if (!empty($cars)){
            $this->messageBus->dispatch(new ImportCars($cars));
        }

    }

}