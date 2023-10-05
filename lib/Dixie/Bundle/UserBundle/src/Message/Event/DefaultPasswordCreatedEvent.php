<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Event;

use Talav\Component\User\Model\UserInterface;

final class DefaultPasswordCreatedEvent
{
    public function __construct(
        public readonly UserInterface $user,
        public readonly int $password
    ) {
    }
}
