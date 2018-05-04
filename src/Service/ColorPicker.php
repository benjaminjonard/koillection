<?php

namespace App\Service;

/**
 * Class ColorPicker
 *
 * @package App\Service
 */
class ColorPicker
{
    /**
     * Pick a random color from an array.
     *
     * @return string
     */
    public function pickRandomColor() : string
    {
        $colors = [];
        $colors[] = 'E3F2FD';
        $colors[] = 'F3E5F5';
        $colors[] = 'FBE9E7';
        $colors[] = 'EEEEEE';
        $colors[] = 'E8EAF6';
        shuffle($colors);

        return array_shift($colors);
    }
}
