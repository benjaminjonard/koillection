<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueDatumLabel extends Constraint
{
    public string $message = '"{{ label }}" label is used multiple times, all labels must be unique';
    public string $mode = 'strict';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}