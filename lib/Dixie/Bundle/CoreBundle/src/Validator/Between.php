<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Between extends Constraint
{
    public function __construct(
        public readonly string $with,
        public readonly bool $property = true,
        public readonly string $message = 'The {{ current }} property does not match the {{ with }} property',
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }
}
