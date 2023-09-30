<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class AgeVerification extends Constraint
{
    /** @var string */
    public $tooYoungMessage = 'age_verification.too_young';

    /** @var string */
    public $dateInFutureMessage = 'age_verification.date_in_future';

    /** @var int */
    public $age = 0;
}
