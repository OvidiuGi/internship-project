<?php

namespace App\Validator;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CnpValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Cnp) {
            throw new UnexpectedTypeException($constraint, Cnp::class);
        }
        $cnpConstant = "279146358279";
        $sum = 0;
        $length = strlen($value)-1;
        for($index = 0; $index < $length; $index++){
            $valueInt = (int)$value[$index];
            $constantInt = (int)$cnpConstant[$index];
            $sum += $valueInt * $constantInt;
        }
        $remainder = $sum % 11;
        $cValue = 0;
        if($remainder == 10){
            $cValue = 1;
        }
        if($remainder < 10){
            $cValue = $remainder;
        }
        if($cValue === (int)$value[$length]){
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}