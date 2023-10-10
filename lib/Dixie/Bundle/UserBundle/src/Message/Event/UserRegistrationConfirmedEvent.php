<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Event;

use Talav\Component\User\Model\UserInterface;

final class UserRegistrationConfirmedEvent
{
    public function __construct(
        public readonly UserInterface $user,
        public readonly bool $is_oauth = false,
        public readonly ?string $oauth_type = null
    ) {
    }
}
