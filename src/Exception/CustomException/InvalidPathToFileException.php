<?php

namespace App\Exception\CustomException;

/**
 * @codeCoverageIgnore
 */
class InvalidPathToFileException extends \Exception
{
    private string $pathToFile;

    public function __construct(
        $message = 'The file does not exist',
        $code = 0,
        \Throwable $previous = null,
        string $pathToFile
    ) {
        $this->pathToFile = $pathToFile;

        parent::__construct($message, $code, $previous);
    }

    public function getPathToFile(): string
    {
        return $this->pathToFile;
    }
}
