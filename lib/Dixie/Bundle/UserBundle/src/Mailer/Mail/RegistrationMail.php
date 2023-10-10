<?php

declare(strict_types=1);

namespace Talav\UserBundle\Mailer\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Talav\Component\User\Model\UserInterface;

final class RegistrationMail extends TemplatedEmail
{
    private ?string $confirmationUrl = null;

    private readonly ?UserInterface $user;

    public function __construct(Headers $headers = null, AbstractPart $body = null)
    {
        parent::__construct($headers, $body);

        $this->textTemplate('@TalavUser/registration/email.txt.twig');
        $this->htmlTemplate('@TalavUser/registration/email.html.twig');
    }

    public function setConfirmationUrl(?string $confirmationUrl): self
    {
        $this->confirmationUrl = $confirmationUrl;

        return $this;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConfirmationUrl(): ?string
    {
        return $this->confirmationUrl;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    /**
     * @return array<mixed>
     */
    public function getContext(): array
    {
        return array_merge([
            'user'             => $this->getUser(),
            'confirmationUrl'  => $this->getConfirmationUrl(),
        ], parent::getContext());
    }
}
