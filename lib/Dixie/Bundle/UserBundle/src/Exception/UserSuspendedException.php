<?php

declare(strict_types=1);

namespace Talav\UserBundle\Exception;

use DateTimeInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Talav\Component\Resource\Exception\SafeMessageException;
use Talav\Component\User\Model\UserInterface;
use Talav\ProfileBundle\Entity\Suspension;

final class UserSuspendedException extends SafeMessageException
{
    protected string $messageDomain = 'TalavUserBundle';

    private const Message = 'authentication.exceptions.user_suspended';

    private Suspension $suspension;

    public function __construct(
        string $message = 'authentication.exceptions.user_suspended',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }



    public static function create(UserInterface $user, Suspension $suspension): UserSuspendedException
    {
//        $self = new self('authentication.exceptions.user_suspended', ['%username%' => $user->getUsername(), '%reason%' => $suspension->getReason()]);
        $self = new self();
        $self->suspension = $suspension;

        return $self;
    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getMessageKey(): string
//    {
//        return self::Message;
//    }


    /**
     * {@inheritdoc}
     */
    public function getMessageData(): array
    {
        $reason = $this->suspension->getReason();
        /** @var DateTimeInterface $date */
        $date = $this->suspension->getSuspendedUntil();

        return [
            'suspension_reason' => $reason,
            'suspended_until' => $date->format('Y-m-d H:i:s'),
        ];
    }
}
