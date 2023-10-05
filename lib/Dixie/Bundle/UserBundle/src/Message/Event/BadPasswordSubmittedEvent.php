<?php

declare(strict_types=1);

namespace Talav\UserBundle\Message\Event;

use Talav\Component\User\Model\UserInterface;

final class BadPasswordSubmittedEvent
{
    public function __construct(
        public readonly UserInterface $user
    ) {
    }
}
