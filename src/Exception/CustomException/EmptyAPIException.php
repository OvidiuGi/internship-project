<?php

namespace App\Exception\CustomException;

class EmptyAPIException extends \Exception
{
    public $message = 'Api empty, nothing to import!';
}
