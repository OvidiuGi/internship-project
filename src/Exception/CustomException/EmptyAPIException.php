<?php

namespace App\Exception\CustomException;

/**
 * @codeCoverageIgnore
 */
class EmptyAPIException extends \Exception
{
    public $message = 'Api empty, nothing to import!';
}
