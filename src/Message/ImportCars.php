<?php

namespace App\Message;

class ImportCars
{
    private array $cars;
    public function __construct(array $cars)
    {
        $this->cars = $cars;
    }

    public function getCars(): array
    {
        return $this->cars;
    }

}