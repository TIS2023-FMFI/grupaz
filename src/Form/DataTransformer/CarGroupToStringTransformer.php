<?php

namespace App\Form\DataTransformer;

use App\Entity\CarGroup;
use App\Repository\CarGroupRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CarGroupToStringTransformer implements DataTransformerInterface
{
    public function __construct(private readonly CarGroupRepository $carGroupRepository) {}

    /**
     * Transforms an object (carGroup) to a string (number).
     *
     * @param ?CarGroup $value
     */
    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        return $value->getGid();
    }

    /**
     * Transforms a string to an object (carGroup).
     *
     * @param  string $value
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($value): ?CarGroup
    {
        // no car group string? It's optional, so that's ok
        if (!$value) {
            return null;
        }

        $carGroup = $this->carGroupRepository
            // query for the issue with this id
            ->findOneByGid($value)
        ;

        if (null === $carGroup) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'Car group with number "%s" does not exist!',
                $value
            ), 0, null, 'car_group.invalid_gid', ['%gid%' => $value]);
        }

        return $carGroup;
    }
}