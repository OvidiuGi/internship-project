<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class Date extends Constraint
{
    public string $message = "The date you provided is not valid. Please try again!";
}