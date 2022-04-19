<?php

namespace App\Command\CustomException;

class EmptyAPIException extends \Exception
{
    public $message = 'Api empty, nothing to import!';
}
