<?php

namespace App\Decrypter;

class CaesarCipher
{
    public string $ch = '';

    public int $key = 0;

    public static function cipher($ch, $key): string
    {
        if (!\ctype_alpha($ch)) {
            return $ch;
        }

        $offset = \ord(\ctype_upper($ch) ? 'A' : 'a');

        return \chr(\fmod(((\ord($ch) + $key) - $offset), 26) + $offset);
    }

    public static function encipher($input, $key): string
    {
        $output = '';

        $inputArr = \str_split($input);
        foreach ($inputArr as $ch) {
            $output .= CaesarCipher::cipher($ch, $key);
        }

        return $output;
    }

    public static function decipher($input, $key): string
    {
        return CaesarCipher::encipher($input, 26 - $key);
    }
}
