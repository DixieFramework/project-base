<?php

declare(strict_types=1);

namespace Talav\UserBundle\Event;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class FormEvent extends Event
{
    private readonly FormInterface $form;

    private readonly Request $request;

    private ?Response $response = null;

    public function __construct(FormInterface $form, Request $request)
    {
        $this->form    = $form;
        $this->request = $request;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
