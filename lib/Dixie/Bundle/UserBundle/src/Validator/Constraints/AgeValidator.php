<?php

declare(strict_types=1);

namespace Talav\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AgeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTime || !$constraint instanceof Age) {
            return;
        }

        $now = new \DateTime();
        $minAgeDate = new \DateTime("-{$constraint->min} years");
        $maxAgeDate = new \DateTime("-{$constraint->max} years");

        $error = false;
        $limit = 0;

        if ($value > $now) {
            $error = $constraint->futureError;
        } else {
            if ($constraint->min > 0 && $value > $minAgeDate) {
                $error = $constraint->minError;
                $limit = $constraint->min;
            }

            if ($value < $maxAgeDate) {
                $error = $constraint->maxError;
                $limit = $constraint->max;
            }
        }

        if ($error) {
            $this->context
                ->buildViolation($error)
                ->setParameter('{{ limit }}', $limit)
                ->addViolation();
        }
    }
}
