<?php

declare(strict_types=1);

namespace Talav\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;
use Talav\Component\User\Model\UserInterface;

final class GetResponseRegistrationEvent extends Event
{
    protected ?Request $request;

    protected UserInterface $user;

    private ?Response $response = null;

    public function __construct(UserInterface $user, Request $request = null)
    {
        $this->user    = $user;
        $this->request = $request;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
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
