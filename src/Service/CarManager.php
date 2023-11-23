<?php

namespace App\Service;

use App\Entity\CarGroup;
use Doctrine\Persistence\ManagerRegistry;

class CarManager {

    /**
     * Function unload all cars in specified $carGroup.
     *
     * @param CarGroup $carGroup
     * @param ManagerRegistry $managerRegistry
     * @return void
     */
    public function unloadCars(CarGroup $carGroup, ManagerRegistry $managerRegistry): void
    {
        foreach ($carGroup->getCars() as $car){
            $car->setStatus(0);
        }
        $managerRegistry->getManager()->flush();
    }

    /**
     * Function try change status on 1 (loaded) to car with $vis and then return true. If car with this $vis is not founded function return false.
     *
     * @param CarGroup $carGroup
     * @param string $vis
     * @param ManagerRegistry $managerRegistry
     * @return bool
     */
    public function loadCar(CarGroup $carGroup, string $vis, ManagerRegistry $managerRegistry): bool
    {
        foreach ($carGroup->getCars() as $car) {
            if ($car->getVis() == $vis) {
                $car->setStatus(1);
                $managerRegistry->getManager()->flush();
                return true;
            }
        }
        return false;
    }

    /**
     * Function checks all cars in $carGroup and if are all loaded then return true.
     *
     * @param CarGroup $carGroup
     * @return bool
     */
    public function allIsLoaded(CarGroup $carGroup): bool
    {
        foreach ($carGroup->getCars() as $car) {
            if ($car->getStatus() == 0) {
                return false;
            }
        }
        return true;
    }
}