<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Entity;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Interfaces\RoleInterface;

interface ProfileInterface extends ResourceInterface
{
	public const DEFAULT_ROLE = RoleInterface::ROLE_USER;
	public const DEFAULT_LANGUAGE = 'en';

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function setUser(UserInterface $user);

	public function getUser();
}
