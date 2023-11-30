<?php
namespace App\Validator\Constraints;

use DateTimeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateRangeValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint): void
    {
        if (!is_array($value)){
            return;
        }

        if(!isset($value['start']) && !isset($value['end'])){
            return;
        }
        if (!$value['start'] instanceof DateTimeInterface && !$value['end'] instanceof DateTimeInterface){
            return;
        }
        if ($value['end'] < $value['start']){
            $this->context->buildViolation($constraint->invalid)
                ->addViolation();
        }
    }
}