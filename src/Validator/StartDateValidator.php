<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StartDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Date) {
            throw new UnexpectedTypeException($constraint, Date::class);
        }
        if (
            preg_match(
                '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/',
                $value,
                $matches
            ) && !empty($matches[0])
        ) {
            return;
        }
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
