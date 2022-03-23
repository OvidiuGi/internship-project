<?php

namespace App\Command\CustomException;

use Throwable;

class EmptyAPIException extends \Exception
{
    public $message = 'Api empty, nothing to import!';
}
