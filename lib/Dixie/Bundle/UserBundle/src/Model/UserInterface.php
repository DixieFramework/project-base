<?php

declare(strict_types=1);

namespace Talav\UserBundle\Model;

use Talav\Component\User\Model\UserInterface as BaseUser;

interface UserInterface extends BaseUser
{
    /**
     * Sets whether to send the creation email or not.
     * (non mapped)
     *
     * @return $this|UserInterface
     */
    public function setSendCreationEmail(bool $send): UserInterface;

    /**
     * Returns whether to send the creation email or not.
     * (non mapped)
     */
    public function getSendCreationEmail(): bool;
}
