<?php

declare(strict_types=1);

namespace Groshy\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidFacebookName extends Constraint
{
    public $message = 'not.valid.facebook.name';
}
