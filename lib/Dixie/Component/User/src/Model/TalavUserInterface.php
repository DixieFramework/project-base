<?php

declare(strict_types=1);

namespace Talav\Component\User\Model;

use DateTimeInterface;
use Serializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUser;
use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\Resource\Model\TimestampableInterface;

interface TalavUserInterface extends PasswordAuthenticatedUserInterface, SymfonyUser, TimestampableInterface, ResourceInterface, Serializable
{
    public function setEmail(string $email): TalavUserInterface;

    public function getEmail(): ?string;

    public function setPassword(?string $password): TalavUserInterface;

    /**
     * Returns whether the user is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Sets whether the user is enabled.
     */
    public function setEnabled(bool $enabled): TalavUserInterface;

    public function getLastLogin(): ?DateTimeInterface;

    public function setLastLogin(?DateTimeInterface $lastLogin): TalavUserInterface;

    /**
     * Sets the plain password.
     * (non mapped)
     */
    public function setPlainPassword(?string $plainPassword): TalavUserInterface;

    /**
     * Returns the plain password.
     * (non mapped)
     */
    public function getPlainPassword(): ?string;
}
