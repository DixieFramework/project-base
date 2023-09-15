<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Google reCaptcha contraint.
 *
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Recaptcha extends Constraint
{
}
