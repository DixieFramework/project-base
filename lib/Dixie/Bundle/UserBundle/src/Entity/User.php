<?php

declare(strict_types=1);

namespace Talav\UserBundle\Entity;

use Talav\Component\User\Model\AbstractUser;
use Talav\UserBundle\Model\UserInterface;

abstract class User extends AbstractUser implements UserInterface
{
    /** ------------ (non mapped) ------------ */
    protected bool $sendCreationEmail = false;

    public function setSendCreationEmail(bool $send): UserInterface
    {
        $this->sendCreationEmail = $send;

        return $this;
    }

    public function getSendCreationEmail(): bool
    {
        return $this->sendCreationEmail;
    }
}
