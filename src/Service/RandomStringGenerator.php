<?php

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
     */
    public function generateString(int $length = 10) : string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';

        for ($i = 0; $i < $length; ++$i) {
            $string .= $chars[mt_rand(0, 61)];
        }

        return $string;
    }
}
