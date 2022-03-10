<?php

namespace App\Validator;

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
        if(strlen($value) === 13){
            return;
        }
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}