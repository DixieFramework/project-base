<?php

declare(strict_types=1);

namespace Talav\Component\User\ValueObject;

use Talav\Component\Resource\Metadata\Resource;
use Webmozart\Assert\Assert;

#[Resource(name: 'value_object_roles')]
class Roles implements \Stringable
{
    public const VALUES = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_SUPER_ADMIN', 'ROLE_CONTENT_MANAGER'];
    public const CHOICES = [
        'authentication.value_object.roles.admin' => 'ROLE_ADMIN',
        'authentication.value_object.roles.user' => 'ROLE_USER',
        'authentication.value_object.roles.super_admin' => 'ROLE_SUPER_ADMIN',
        'authentication.value_object.roles.content_manager' => 'ROLE_CONTENT_MANAGER',
    ];

    private readonly array $roles;

    private function __construct(array $roles = ['ROLE_USER'])
    {
        Assert::notEmpty($roles, 'authentication.validations.empty_roles');
        foreach ($roles as $role) {
            Assert::inArray($role, self::VALUES, 'authentication.validations.invalid_roles');
        }

        $roles[]           = 'ROLE_USER';
        $this->roles = array_unique($roles);
    }

    public function __toString(): string
    {
        return implode(',', $this->roles);
    }

    public function getTranslationKey(): string
    {
        return match (true) {
            $this->contains('ROLE_ADMIN') => 'authentication.value_object.roles.admin',
            $this->contains('ROLE_SUPER_ADMIN') => 'authentication.value_object.roles.super_admin',
            $this->contains('ROLE_CONTENT_MANAGER') => 'authentication.value_object.roles.content_manager',
            default => 'authentication.value_object.roles.user'
        };
    }

    public function toArray(): array
    {
        return $this->roles;
    }

    public static function fromArray(array $roles): self
    {
        return new self($roles);
    }

    public static function superAdmin(): self
    {
        return new self(['ROLE_SUPER_ADMIN']);
    }

    public static function admin(): self
    {
        return new self(['ROLE_ADMIN']);
    }

    public static function developer(): self
    {
        return new self();
    }

    public static function contentManager(): self
    {
        return new self(['ROLE_USER', 'ROLE_CONTENT_MANAGER']);
    }

    public function equals(array|self $roles): bool
    {
        if ($roles instanceof self) {
            return $roles->roles === $this->roles;
        }

        return $roles === $this->roles;
    }

    public function contains(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

//    public function removeElement(string $role): bool
//    {
//        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
//            unset($this->roles[$key]);
//            $this->roles = array_values($this->roles);
//        }
//    }
}
