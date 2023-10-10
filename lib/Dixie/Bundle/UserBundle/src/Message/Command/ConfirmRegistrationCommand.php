<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Command;

final class ConfirmRegistrationCommand
{
    public function __construct(
        public readonly string $token
    ) {
    }
}
