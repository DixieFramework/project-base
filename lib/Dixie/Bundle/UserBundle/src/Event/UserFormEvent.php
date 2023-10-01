<?php

declare(strict_types=1);

namespace Talav\UserBundle\Event;

use Talav\Component\User\Model\UserInterface;
use Talav\UserBundle\Event\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class UserFormEvent extends FormEvent
{
    private UserInterface $user;

    public function __construct(UserInterface $user, FormInterface $form, Request $request)
    {
        parent::__construct($form, $request);

        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
