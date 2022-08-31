<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Field;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

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
        foreach ($value as $element) {
            $label = $element instanceof Field ? $element->getName() : $element->getLabel();

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
