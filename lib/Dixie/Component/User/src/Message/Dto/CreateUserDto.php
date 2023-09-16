<?php

declare(strict_types=1);

namespace Talav\Component\User\Message\Dto;

use Talav\Component\Resource\Model\DomainEventInterface;

// @todo, find a better approach to create DTO
final class CreateUserDto implements DomainEventInterface
{
//    public bool $active = true;
//    public ?string $firstName = null;
//    public ?string $lastName = null;
//    public ?string $username = null;
//    public ?string $email = null;
//    public ?string $password = null;
    public bool $active = true;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $password = null;

    /**
     * CreateUserRequest constructor.
     *
     * @param string $username
     * @param string $email
     * @param string $plainPassword
     * @param string $role
     */
    public function __construct(string $username, string $email, string $plainPassword, bool $active = true, string $firstName = '', string $lastName = '')
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $plainPassword;
        $this->active = $active;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
