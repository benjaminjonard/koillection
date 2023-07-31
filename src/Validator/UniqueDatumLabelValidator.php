<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Datum;
use App\Entity\Field;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueDatumLabelValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueDatumLabel) {
            throw new UnexpectedTypeException($constraint, UniqueDatumLabel::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $labels = [];
        foreach ($value as $element) {
            $label = match (true) {
                $element instanceof Field => $element->getName(),
                $element instanceof Datum => $element->getLabel(),
                is_array($element) && isset($element['name']) => $element['name'],
                default => throw new UnexpectedValueException($element, 'Field|Datum|array'),
            };

            if (\in_array($label, $labels)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ label }}', $label)
                    ->addViolation();
            } else {
                $labels[] = $label;
            }
        }
    }
}
