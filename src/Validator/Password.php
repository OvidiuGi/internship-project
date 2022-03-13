<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Password extends Constraint
{
    public string $message = 'Password not valid!';
}