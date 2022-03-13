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

        $regexResponse = preg_match_all(
            '/^([1-8])([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])(0[0-9]|[1-3]\d|4[0-8])(\d{3})([0-9])$/',
            $value,
            $matches
        );

        if(!empty($matches[0])) {
            $cnpConstant = "279146358279";
            $sum = 0;
            $length = strlen($value);

            for ($index = 0; $index < $length - 1; $index++) {
                $valueInt = (int)$value[$index];
                $constantInt = (int)$cnpConstant[$index];
                $sum += $valueInt * $constantInt;
            }

            $remainder = $sum % 11;
            $cValue = 0;

            if ($remainder == 10) {
                $cValue = 1;
            }
            if ($remainder < 10) {
                $cValue = $remainder;
            }
            if ($cValue === (int)$value[$length - 1] && $regexResponse) {
                return;
            }
        }
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}