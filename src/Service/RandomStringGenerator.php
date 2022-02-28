<?php

declare(strict_types=1);

namespace App\Service;

class RandomStringGenerator
{
    public function generate(int $length = 10): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';

        for ($i = 0; $i < $length; ++$i) {
            $string .= $chars[random_int(0, 61)];
        }

        return $string;
    }
}
