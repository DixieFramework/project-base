<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Event;

use Talav\Component\User\Model\UserInterface;

final class UserUnbannedEvent
{
    public function __construct(
        public readonly UserInterface $user
    ) {
    }
}
