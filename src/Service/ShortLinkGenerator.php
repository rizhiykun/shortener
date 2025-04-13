<?php

namespace App\Service;

class ShortLinkGenerator
{
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generateShortLink(?int $length = null): string
    {
        $length ??= random_int(4, 8);

        $result = '';
        $maxIndex = strlen(self::ALPHABET) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= self::ALPHABET[random_int(0, $maxIndex)];
        }

        return $result;
    }
}