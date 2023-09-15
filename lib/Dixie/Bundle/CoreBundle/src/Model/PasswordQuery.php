<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Model;

use Talav\CoreBundle\Enums\StrengthLevel;

/**
 * Class to hold password data.
 */
class PasswordQuery
{
    public function __construct(
        public string $password = '',
        public StrengthLevel $strength = StrengthLevel::NONE,
        public ?string $email = null,
        public ?string $user = null
    ) {
    }

    public function getInputs(): array
    {
        return \array_filter([$this->email, $this->user]);
    }
}
