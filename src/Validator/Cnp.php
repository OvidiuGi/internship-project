<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Cnp extends Constraint
{
    public $message = "This is not a valid CNP";

}