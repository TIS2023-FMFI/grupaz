<?php

namespace App\MessageHandler;

use App\Entity\Car;
use App\Entity\CarGroup;
use App\Message\ImportCar;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function Symfony\Component\Clock\now;

#[AsMessageHandler]
class ImportCarHandler
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(ImportCar $uploadCar): void
    {
        $manager = $this->managerRegistry->getManager();
        $carGroup = null;
        if($uploadCar->getGid() !== null)
        {
            $carGroup = $manager->getRepository(CarGroup::class)->findOneByGid($uploadCar->getGid());
            if (!$carGroup)
            {
                $carGroup = new CarGroup();
                $carGroup->setGid($uploadCar->getGid());
                $carGroup->setImportTime(now());
                $carGroup->setStatus(CarGroup::STATUS_FREE);
                $manager->persist($carGroup);
            }
            $carGroup->setReceiver($uploadCar->getReceiver());
            $carGroup->setDestination($uploadCar->getDestination());
        }
        if($uploadCar->getVis() !== null)
        {
            $car = $manager->getRepository(Car::class)->findOneByVis($uploadCar->getVis());
            if (!$car)
            {
                $car = new Car();
                $car->setVis($uploadCar->getVis());
                $car->setStatus(Car::STATUS_FREE);
                $car->setIsDamaged(Car::STATUS_IS_NEW);
                $manager->persist($car);
            }
            $car->setCarGroup($carGroup);
        }
        $manager->flush();
    }

}