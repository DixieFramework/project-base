<?php

declare(strict_types=1);

namespace Talav\Component\User\Util;

use Talav\Component\User\Model\UserInterface;

interface PasswordUpdaterInterface
{
    /**
     * Updates the hashed password in the user when there is a new password.
     *
     * The implement should be a no-op in case there is no new password (it should not erase the
     * existing hash with a wrong one).
     *
     * @return void
     */
    public function hashPassword(UserInterface $user);
}
