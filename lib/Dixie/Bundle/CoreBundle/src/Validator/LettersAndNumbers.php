<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LettersAndNumbers extends Constraint
{
    public string $message = 'Username may only contain unaccented letters, underscores, and numbers. No spaces or symbols';
}
