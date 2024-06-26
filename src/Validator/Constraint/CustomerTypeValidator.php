<?php

namespace Celtic34fr\ContactCore\Validator\Constraint;

use Celtic34fr\ContactCore\Enum\CustomerEnums;
use Celtic34fr\ContactCore\Validator\Constraint\ContainsAlphanumeric;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CustomerTypeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CustomerType) {
            throw new UnexpectedTypeException($constraint, ContainsAlphanumeric::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        // access your configuration options like this:
        if ('strict' === $constraint->mode) {
            // ...
        }

        /** logique de la validation */
        if (!CustomerEnums::isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
