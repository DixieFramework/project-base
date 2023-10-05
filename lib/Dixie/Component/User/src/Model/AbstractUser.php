<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use DateInterval;
use DateTime;
use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\TimestampableTrait;
use function serialize;
use function unserialize;

abstract class AbstractUser implements UserInterface
{
    use TimestampableTrait;
    use ResourceTrait;

    public const DEFAULT_ROLE = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    protected ?string $username = null;

    protected ?string $usernameCanonical = null;

    protected bool $enabled;

    protected string $salt;

    protected ?string $password = null;

    protected ?DateTimeInterface $lastLogin = null;

    protected ?string $loginAttemptsResetToken = null;

    protected ?string $passwordResetToken = null;

    protected ?DateTimeInterface $passwordRequestedAt = null;

    protected ?string $confirmationToken = null;

    protected bool $verified = false;

    protected iterable $arrRoles = [];

    protected ?string $email = null;

    protected ?string $emailCanonical = null;

    protected ?string $firstName = null;

    protected ?string $lastName = null;

    protected ?\DateTimeImmutable $lastLoginAt = null;

    protected ?string $lastLoginIp = null;

    public Collection $oauthAccounts;

    /* ---------- (non mapped) ---------- */
    protected ?string $plainPassword = null;

    public function __construct()
    {
        $this->oauthAccounts = new ArrayCollection();
        $this->salt = base_convert(bin2hex(random_bytes(20)), 16, 36);
        $this->enabled = false;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(?string $usernameCanonical): void
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTimeInterface $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getLoginAttemptsResetToken(): ?string
    {
        return $this->loginAttemptsResetToken;
    }

    public function setLoginAttemptsResetToken(?string $token): self
    {
        $this->loginAttemptsResetToken = $token;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    public function getPasswordRequestedAt(): ?DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?DateTimeInterface $passwordRequestedAt): void
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    /**
     * @throws Exception
     */
    public function isPasswordRequestNonExpired(DateInterval $ttl): bool
    {
        if (null === $this->passwordRequestedAt) {
            return false;
        }
        $threshold = new DateTime();
        $threshold->sub($ttl);

        return $threshold <= $this->passwordRequestedAt;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     */
    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    public function getRoles(): array
    {
        return $this->arrRoles;
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);
        if (!$this->hasRole($role)) {
            $this->arrRoles[] = $role;
        }
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->arrRoles, true)) {
            unset($this->arrRoles[$key]);
            $this->arrRoles = array_values($this->arrRoles);
        }
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function setSuperAdmin($boolean): void
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(?string $emailCanonical): void
    {
        $this->emailCanonical = $emailCanonical;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = match (true) {
            null !== $lastLoginAt => \DateTimeImmutable::createFromInterface($lastLoginAt),
            default => null
        };

        return $this;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    // this method is required by Symfony UserInterface
    public function eraseCredentials()
    {
    }

    public function getOauthAccounts(): Collection
    {
        return $this->oauthAccounts;
    }

    public function getOAuthAccount(string $provider): ?UserOAuthInterface
    {
        if ($this->oauthAccounts->isEmpty()) {
            return null;
        }
        $filtered = $this->oauthAccounts->filter(function (UserOAuthInterface $oauth) use ($provider): bool {
            return $provider === $oauth->getProvider();
        });
        if ($filtered->isEmpty()) {
            return null;
        }

        return $filtered->current();
    }

    public function addOAuthAccount(UserOAuthInterface $oauth): void
    {
        if (!$this->oauthAccounts->contains($oauth)) {
            $this->oauthAccounts->add($oauth);
            $oauth->setUser($this);
        }
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): UserInterface
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    /**
     * The serialized data have to contain the fields used by the equals method and the username.
     */
    public function serialize(): string
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));
        [
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
        ] = $data;
    }

    protected function hasExpired(?\DateTimeInterface $date): bool
    {
        return null !== $date && new \DateTime() >= $date;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsernameCanonical();
    }
}
