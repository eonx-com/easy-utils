<?php
declare(strict_types=1);

namespace EonX\EasyUtils\Math\Validator;

use EonX\EasyUtils\Math\Constraint\Number;
use EonX\EasyUtils\Math\ValueObject\Number as NumberValueObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class NumberValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($constraint instanceof Number === false) {
            throw new UnexpectedTypeException($constraint, Number::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($value instanceof NumberValueObject === false) {
            throw new UnexpectedValueException($value, NumberValueObject::class);
        }

        $validator = $this->context->getValidator()
            ->inContext($this->context);

        $validator->validate($value->getRawValue(), $constraint->constraints);
    }
}
