<?php

declare(strict_types=1);

namespace Talav\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Age extends Constraint
{
    public $min = 0;
    public $max = 150;

    public $futureError = 'person.validation.age.future';
    public $minError = 'person.validation.age.min';
    public $maxError = 'person.validation.age.max';

    public function validatedBy()
    {
        return AgeValidator::class;
    }
}
