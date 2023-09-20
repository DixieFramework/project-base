<?php

declare(strict_types=1);

namespace Talav\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;
use Talav\Component\User\Model\UserInterface;

class UserEvent extends Event
{
	protected ?Request $request = null;

	protected readonly UserInterface $user;

	public function __construct(UserInterface $user, Request $request = null)
	{
		$this->user    = $user;
		$this->request = $request;
	}

	public function getUser(): UserInterface
	{
		return $this->user;
	}

	public function getRequest(): ?Request
	{
		return $this->request;
	}
}