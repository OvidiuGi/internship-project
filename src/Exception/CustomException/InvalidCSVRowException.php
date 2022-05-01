<?php

namespace App\Exception\CustomException;

/**
 * @codeCoverageIgnore
 */
class InvalidCSVRowException extends \Exception
{
    private array $row;

    public function __construct($message, $code, \Throwable $previous, array $row)
    {
        $this->row = $row;

        parent::__construct($message, $code, $previous);
    }

    public function getRow(): array
    {
        return $this->row;
    }
}
