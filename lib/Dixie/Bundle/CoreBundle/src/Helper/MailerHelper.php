<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Helper;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\{Address, Email};
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\{LoaderError, RuntimeError, SyntaxError};

final class MailerHelper
{
    public function __construct(
        private readonly Environment $twig,
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly LoggerInterface $logger,
        #[Autowire('%mailer_user_name%')]
        private readonly string $fromName,
        #[Autowire('%mailer_user_email%')]
        private readonly string $fromEmail,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function createEmail(string $template, array $data = []): Email
    {
        $html = $this->twig->render(
            name: $template,
            context: [
                ...$data,
                '_format' => 'html',
                '_layout' => '@TalavCore/mailing/base.html.twig',
            ]
        );

        $text = $this->twig->render(
            name: $template,
            context: [
                ...$data,
                '_format' => 'text',
                '_layout' => '@TalavCore/mailing/base.text.twig',
            ]
        );

        return (new Email())
            ->from(new Address(
                $this->fromEmail,
                $this->fromName
            ))
            ->html($html)
            ->text($text);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendNotificationEmail(
        object $event,
        string $template,
        string $subject,
        array $subject_parameters = [],
        string $domain = 'messages'
    ): void {
        if (! property_exists($event, 'user')) {
            throw new \RuntimeException('Event must have a reference to the user !');
        }

        /** @var UserInterface $user */
        $user = $event->user;
        $this->send(
            $this->createEmail(
                template: $template,
                data: [
                    'user' => $user,
                    'event' => $event,
                ]
            )->subject($this->translator->trans(
                id: $subject,
                parameters: $subject_parameters,
                domain: $domain
            ))->to(new Address($user->getEmailCanonical(), (string)$user->getUsername()))
        );
    }

    public function send(Email $email): void
    {
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }
}
