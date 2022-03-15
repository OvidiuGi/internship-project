<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Cnp extends Constraint
{
    public string $message = "This is not a valid CNP";
}
