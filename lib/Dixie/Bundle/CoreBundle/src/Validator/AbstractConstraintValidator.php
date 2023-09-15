<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Abstract constraint validator.
 *
 * @template T of Constraint
 */
abstract class AbstractConstraintValidator extends ConstraintValidator
{
    /**
     * Constructor.
     *
     * @param class-string<T> $className the constraint class
     */
    public function __construct(private readonly string $className)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!\is_a($constraint, $this->className)) {
            throw new UnexpectedTypeException($constraint, $this->className);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;
        if ('' === $value) {
            return;
        }

        $this->doValidate($value, $constraint);
    }

    /**
     * Performs validation.
     *
     * @psalm-param T $constraint
     */
    abstract protected function doValidate(string $value, Constraint $constraint): void;
}
