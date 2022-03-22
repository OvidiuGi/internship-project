<?php

namespace App\Command\CustomException;

use Throwable;

class EmptyAPIException extends \Exception
{
    public $message = 'api empty';
}
