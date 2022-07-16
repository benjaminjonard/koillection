<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueDatumLabelValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueDatumLabel) {
            throw new UnexpectedTypeException($constraint, UniqueDatumLabel::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $labels = [];
        foreach ($value as $position => $datum) {
            $label = $datum->getLabel();
            if (in_array($label, $labels)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ label }}', $label)
                    ->addViolation();
            } else {
                $labels[] = $label;
            }
        }
    }
}