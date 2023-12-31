<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use DateInterval;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\User\ValueObject\Username;

interface UserInterface extends SymfonyUserInterface, PasswordAuthenticatedUserInterface, ResourceInterface
{
    /**
     * Gets the username.
     */
//    public function setUsername(?string $username): void;
    public function getUsername(): ?Username;

    /**
     * Sets the username.
     */
    public function setUsername(Username|string $username): self;

    /**
     * Gets the canonical username in search and sort queries.
     */
    public function getUsernameCanonical(): ?string;

    /**
     * Sets the canonical username.
     */
    public function setUsernameCanonical(?string $usernameCanonical): void;

    /**
     * Sets the email.
     */
    public function setEmail(?string $email): void;

    /**
     * Gets the email.
     */
    public function getEmail(): ?string;

    /**
     * Gets the canonical email in search and sort queries.
     */
    public function getEmailCanonical(): ?string;

    /**
     * Sets the canonical email.
     */
    public function setEmailCanonical(?string $emailCanonical): void;

    public function getLastLogin(): ?DateTimeInterface;

    public function setLastLogin(?DateTimeInterface $time): void;

    public function getConfirmationToken(): ?string;

    public function setConfirmationToken(?string $confirmationToken): self;

	public function isVerified(): bool;

	public function setVerified(bool $verified): self;

    /**
     * Returns all the roles the user has assigned.
     */
    public function getRoleObjects();

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     */
    public function hasRole(string $role): bool;

    /**
     * Adds a role to the user.
     */
    public function addRole(string $role): void;

    /**
     * Removes a role to the user.
     */
//    public function removeRole(string $role): void;

    /**
     * @return Collection|UserOAuthInterface[]
     */
    public function getOAuthAccounts(): Collection;

    public function getOAuthAccount(string $provider): ?UserOAuthInterface;

    public function addOAuthAccount(UserOAuthInterface $oauth): void;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function isBanned(): bool;

    public function setIsBanned(bool $isBanned): self;

    public function getBannedAt(): ?\DateTimeInterface;

    public function setBannedAt(?\DateTimeInterface $bannedAt): self;

    public function getLastLoginAt(): ?\DateTimeInterface;

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self;

    public function getLastLoginIp(): ?string;

    public function setLastLoginIp(?string $lastLoginIp): self;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): self;

	public function getSalt(): string;

	public function setSalt(string $salt): self;

    /**
     * Tells if the the given user has the super admin role.
     */
    public function isSuperAdmin(): bool;

    /**
     * Sets the super admin status.
     */
    public function setSuperAdmin(): void;

    /**
     * Sets the plain password.
     * (non mapped)
     */
    public function setPlainPassword(?string $plainPassword): UserInterface;

    /**
     * Returns the plain password.
     * (non mapped)
     */
    public function getPlainPassword(): ?string;
}
