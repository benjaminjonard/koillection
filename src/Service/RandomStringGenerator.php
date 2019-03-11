<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Class RandomStringGenerator
 *
 * @package App\Service
 */
class RandomStringGenerator
{
    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function generateString(int $length = 10) : string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';

        for ($i = 0; $i < $length; ++$i) {
            $string .= $chars[random_int(0, 61)];
        }

        return $string;
    }
}
