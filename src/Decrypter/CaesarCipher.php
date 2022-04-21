<?php

namespace App\Decrypter;

class CaesarCipher
{
    public string $ch = '';

    public int $key = 0;

    public static function cipher(string $ch, int $key): string
    {
        if (!\ctype_alpha($ch)) {
            return $ch;
        }

        $offset = \ord(\ctype_upper($ch) ? 'A' : 'a');

        return \chr(\fmod(((\ord($ch) + $key) - $offset), 26) + $offset);
    }

    public static function encipher(string $input, int $key): string
    {
        $output = '';

        $inputArr = \str_split($input);
        foreach ($inputArr as $ch) {
            $output .= CaesarCipher::cipher($ch, $key);
        }

        return $output;
    }

    public static function decipher(string $input, int $key): string
    {
        return CaesarCipher::encipher($input, 26 - $key);
    }
}
