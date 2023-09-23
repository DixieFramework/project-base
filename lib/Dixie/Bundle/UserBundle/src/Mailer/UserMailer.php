<?php

declare(strict_types=1);

namespace Talav\UserBundle\Mailer;

use InvalidArgumentException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\UserRelation;
use Twig\Environment;

class UserMailer implements UserMailerInterface
{
    private const NO_REPLY_EMAIL_ADDRESS = 'noreply@bewelcome.org';
    private const MESSAGE_EMAIL_ADDRESS = 'message@bewelcome.org';
    private const GROUP_EMAIL_ADDRESS = 'group@bewelcome.org';
    private const PASSWORD_EMAIL_ADDRESS = 'password@bewelcome.org';
    private const SIGNUP_EMAIL_ADDRESS = 'signup@bewelcome.org';

    protected MailerInterface $mailer;

    protected UrlGeneratorInterface $router;

    protected Environment $twig;

    protected iterable $parameters;

    public function __construct(
        MailerInterface $mailer,
        UrlGeneratorInterface $router,
        Environment $twig,
        protected readonly TranslatorInterface $translator,
        array $parameters
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->parameters = $parameters;
    }

    /**
     * Send notification for special relation (friends and family).
     */
    public function sendRelationNotification(UserRelation $relation): bool
    {
        $parameters = [];
        $parameters['sender'] = $relation->getOwner();
        $parameters['receiver'] = $relation->getReceiver();
        $parameters['comment'] = $relation->getCommentText();
        $parameters['subject'] = [
            'translationId' => 'email.subject.relation',
            'parameters' => [
                'username' => $relation->getOwner()->getUsername(),
            ],
        ];

        return $this->sendTemplateEmail(
            $this->getTalavAddress($relation->getOwner(), self::NO_REPLY_EMAIL_ADDRESS),
            $relation->getReceiver(),
            'relation.notification',
            $parameters
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user): void
    {
        $template = $this->parameters['template']['confirmation'];
        $url = $this->router->generate(
            'fos_user_registration_confirm',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];
        $this->sendMessage(
            $template,
            $context,
            $this->parameters['from_email']['confirmation'],
            (string) $user->getEmail()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(UserInterface $user): void
    {
        $template = '@TalavUser/email/reset.twig';
        $url = $this->router->generate(
            'talav_user_reset_password',
            ['token' => $user->getPasswordResetToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];
        $this->sendMessage($template, $context, $user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function sendRegistrationSuccessfulEmail(UserInterface $user): void
    {
        $template = '@TalavUser/email/welcome.twig';
        $context = [
            'user' => $user,
        ];
        $this->sendMessage($template, $context, $user->getEmail());
    }

    protected function sendMessage($templateName, $context, $toEmail): void
    {
        $template = $this->twig->load($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = '';
        if ($template->hasBlock('body_html', $context)) {
            $htmlBody = $template->renderBlock('body_html', $context);
        }
        $message = (new Email())
            ->subject($subject)
            ->from(new Address($this->parameters['email'], $this->parameters['name']))
            ->to($toEmail);
        if (!empty($htmlBody)) {
            $message->html($htmlBody);
        }
        $message->text($textBody);
        $this->mailer->send($message);
    }

    /**
     * Used for messages and requests notifications to allow recipients to distinguish between those
     * and other notifications.
     */
    private function getTalavAddressWithUsername(UserInterface $sender): Address
    {
        return new Address(self::MESSAGE_EMAIL_ADDRESS, $sender->getUsername() . ' [BeWelcome]');
    }

    /**
     * Used for all notifications except messages and requests notifications to allow recipients to distinguish between
     * those notifications.
     *
     * @param mixed $email
     */
    private function getTalavAddress(UserInterface $sender, $email): Address
    {
        return new Address($email, 'BeWelcome - ' . $sender->getUsername());
    }

    /**
     * @param UserInterface|Address|string $sender
     * @param UserInterface|Address        $receiver
     * @param string                $template
     * @param mixed                 $parameters
     *
     * @return bool
     */
    private function sendTemplateEmail($sender, $receiver, string $template, array $parameters): bool
    {
        $currentLocale = $this->translator->getLocale();
        $success = true;
        $locale = 'en';
        if ($receiver instanceof \Talav\UserBundle\Model\UserInterface) {
            $this->setTranslatorLocale($receiver);
            $locale = $receiver->getPreferredLanguage()->getShortCode();
            $parameters['receiver'] = $receiver;
            $receiver = new Address($receiver->getEmail(), $receiver->getUsername());
        } elseif (!$receiver instanceof Address) {
            $message = sprintf('$receiver must be an instance of %s or %s.', \Talav\UserBundle\Model\UserInterface::class, Address::class);
            throw new InvalidArgumentException($message);
        }

        $parameters['template'] = $template;
        $parameters['receiverLocale'] = $locale;
        $subject = $parameters['subject'];
        $subjectParams = [];
        if (\is_array($subject)) {
            $subjectParams = $subject['parameters'];
            $subject = $subject['translationId'];
        }
        $subject = $this->translator->trans($subject, $subjectParams);
        $email = (new TemplatedEmail())
            ->to($receiver)
            ->subject($subject)
            ->htmlTemplate('emails/' . $template . '.html.twig')
            ->context($parameters);

        if (isset($parameters['datesent'])) {
            $email->date($parameters['datesent']);
        }

        if (!\is_string($sender) && !$sender instanceof Address) {
            $sender = $email->from($this->getTalavAddressWithUsername($sender));
        }
        $email->from($sender);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $success = false;
        }
        $this->translator->setLocale($currentLocale);

        return $success;
    }
}
