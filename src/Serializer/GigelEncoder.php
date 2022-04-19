<?php

namespace App\Serializer;

use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class GigelEncoder implements EncoderInterface
{
    public const FORMAT = 'gigel';

    public function encode($data, string $format, array $context = []): string
    {
        $transformed = ['greeting' => 'Salutare sunt Gigel'];

        return \json_encode($transformed);
    }

    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

    /**
     * @throws Exception
     */
    public function decode(string $data, string $format, array $context = []): string
    {
        throw new Exception(BadRequestException::class);
    }

    public function supportsDecoding(string $format): bool
    {
        return false;
    }
}
