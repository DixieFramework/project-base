<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use DateTimeInterface;

use Talav\Component\Resource\Model\TimestampableTrait;
use function serialize;
use function unserialize;
abstract class AbstractUser implements TalavUserInterface
{
    use TimestampableTrait;

    protected ?string            $email     = null;
    protected ?string            $password  = null;
    protected bool               $enabled   = false;
    protected ?DateTimeInterface $lastLogin = null;

    /* ---------- (non mapped) ---------- */
    protected ?string $plainPassword = null;


    public function __toString(): string
    {
        return $this->email ?: 'New user';
    }

    public function setEmail(string $email): TalavUserInterface
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(?string $password): TalavUserInterface
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): TalavUserInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): TalavUserInterface
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTimeInterface $lastLogin): TalavUserInterface
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @TODO Remove
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function serialize(): ?string
    {
        return serialize($this->__serialize());
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->email,
            $this->password,
        ];
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data, ['allowed_classes' => false]));
    }

    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->email,
            $this->password,
        ] = $data;
    }
}
