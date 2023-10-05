<?php

declare(strict_types=1);

namespace Talav\SettingsBundle\Tests\Functional\Model;

use Talav\SettingsBundle\Model\SettingsOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements SettingsOwnerInterface, UserInterface
{
    protected $id;

    protected $password;

    protected $username;

    public function __construct($id, $username, $password)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
    }

    public function getId()
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
        return true;
    }

    public function getAclRoles()
    {
        return ['ROLE_USER', 'ROLE_ADMIN'];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return '';
    }
}
