<?php

namespace App\Serializer;

use App\Entity\Car;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CarNormalizer implements NormalizerInterface
{

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        /* @var Car $object*/
        return [
            'vis' => $object->getVis(),
            'car_status' => $object->getStatus(),
            'car_group' => $object->getCarGroup()->getGid(),
            'front_licence_plate' => $object->getCarGroup()->getFrontLicensePlate(),
            'back_licence_plate' => $object->getCarGroup()->getBackLicensePlate(),
            'destination' => $object->getCarGroup()->getDestination(),
            'receiver' => $object->getCarGroup()->getReceiver(),
            'exported_time' => $object->getCarGroup()->getExportTime()->format('Y-m-d'),
            'note' => $object->getNote(),
            'replaced_car' => $object->getReplacedCar(),
            'is_damaged' => $object->getIsDamaged(),
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Car;
    }
}