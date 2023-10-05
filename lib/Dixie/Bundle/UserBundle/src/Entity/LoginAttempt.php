<?php

declare(strict_types=1);

namespace Talav\UserBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\TimestampableTrait;
use Talav\Component\User\Model\UserAwareInterface;
use Talav\Component\User\Model\UserAwareTrait;

class LoginAttempt implements ResourceInterface, UserAwareInterface
{
    use ResourceTrait;
    use UserAwareTrait;
    use TimestampableTrait;

    public static function createFor(User $user): self
    {
        return (new self())->setUser($user);
    }
}
