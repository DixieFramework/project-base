<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Talav\CoreBundle\Enums\StrengthLevel;
use Talav\CoreBundle\Validator\Strength;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Trait to translate {@see StrengthLevel}.
 */
trait StrengthLevelTranslatorTrait
{
    use TranslatorTrait;

    /**
     * Add a violation.
     */
    public function addStrengthLevelViolation(ExecutionContextInterface $context, Strength $constraint, StrengthLevel $minimum, StrengthLevel $score): void
    {
        $parameters = [
            '%minimum%' => $this->translateLevel($minimum),
            '%score%' => $this->translateLevel($score),
        ];
        $context->buildViolation($constraint->strength_message)
            ->setCode(Strength::IS_STRENGTH_ERROR)
            ->setParameters($parameters)
            ->addViolation();
    }

    /**
     * Translate an invalid strength value.
     */
    public function translateInvalidLevel(StrengthLevel|int $value): string
    {
        if ($value instanceof StrengthLevel) {
            $value = $value->value;
        }

        return $this->trans('password.strength_invalid', [
            '%allowed%' => \implode(', ', StrengthLevel::values()),
            '%value%' => $value,
        ], 'validators');
    }

    /**
     * Translate the strength level.
     */
    public function translateLevel(StrengthLevel $level): string
    {
        return $this->trans($level);
    }

    /**
     * Translate the score.
     */
    public function translateScore(StrengthLevel $minimum, StrengthLevel $score): string
    {
        return $this->trans('password.strength_level', [
            '%minimum%' => $this->translateLevel($minimum),
            '%score%' => $this->translateLevel($score),
        ], 'validators');
    }
}
