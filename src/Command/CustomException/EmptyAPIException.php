<?php

namespace App\Command\CustomException;

class EmptyAPIException extends \Exception
{
    private array $data;

    public function __construct($message = "", $code = 0, Throwable $previous = null, array $data)
    {
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    public function getData(): array
    {
        return $this->data;
    }
}