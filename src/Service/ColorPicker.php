<?php

declare(strict_types=1);

namespace App\Service;

class ColorPicker
{
    public function pickRandomColor() : string
    {
        $colors = [];
        $colors[] = 'E3F2FD';
        $colors[] = 'F3E5F5';
        $colors[] = 'FBE9E7';
        $colors[] = 'EEEEEE';
        $colors[] = 'E8EAF6';
        shuffle($colors);

        return \array_shift($colors);
    }
}
