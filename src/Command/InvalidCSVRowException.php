<?php

namespace App\Command;

use Throwable;

class InvalidCSVRowException extends \Exception
{
    private array $row;

    public function __construct($message = '', $code = 0, Throwable $previous = null, array $row)
    {
        $this->row = $row;

        parent::__construct($message, $code, $previous);
    }

    public function getRow(): array
    {
        return $this->row;
    }
}
