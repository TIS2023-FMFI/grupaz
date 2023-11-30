<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DateRange extends Constraint
{
    public string $invalid = 'form.date_range.invalid';
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}