<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Model;

use Talav\Component\Resource\Model\ResourceInterface;
use Talav\Component\User\Model\UserInterface;

interface ProfileInterface extends ResourceInterface
{

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;

    public function setUser(UserInterface $user);

	public function getUser();
}
