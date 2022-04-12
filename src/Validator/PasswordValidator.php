<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if (count(explode(' ', $value)) > 1) {
            $constraint->message = 'Password not valid!';
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        if (
            preg_match(
                '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!#.\/%*?&])[A-Za-z\d@$.\/#!%*?&]{8,}$/',
                $value,
                $matches
            ) && !empty($matches[0])
        ) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
