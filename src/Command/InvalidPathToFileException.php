<?php

namespace App\Command;

use Throwable;

class InvalidPathToFileException extends \Exception
{
    private string $pathToFile;

    public function __construct($message = "", $code = 0, Throwable $previous = null, string $pathToFile)
    {
        $this->pathToFile = $pathToFile;

        parent::__construct($message, $code, $previous);
    }

    public function getPathToFile(): string
    {
        return $this->pathToFile;
    }
}