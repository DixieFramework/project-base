<?php

declare(strict_types=1);

namespace Talav\UserBundle\Doctrine\EntityListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Enums\Importance;
use Talav\CoreBundle\Mime\RegistrationEmail;
use Talav\CoreBundle\Service\EmailVerifier;
use Talav\UserBundle\Entity\User;

//#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
//#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
//#[AsDoctrineListener(event: Events::postFlush)]
class UserEmailEntityListener// implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /** @var UserInterface[] */
    private array $queue = [];

    public function __construct(
		private readonly EmailVerifier $emailVerifier,
		private readonly TranslatorInterface $translator,
	    #[Autowire('%mailer_user_name%')]
	    private readonly string $fromName,
	    #[Autowire('%mailer_user_email%')]
	    private readonly string $fromEmail,
    )
    {
    }

    public function prePersist(UserInterface $user, PrePersistEventArgs $eventArgs): void
    {
        $this->handleUserEmail($user);
    }

    public function preUpdate(UserInterface $user, PreUpdateEventArgs $args): void
    {
        if (!$args->hasChangedField('email')) {
            return;
        }

        $this->handleUserEmail($user);
    }

    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        if ([] === $this->queue) {
            return;
        }

        foreach ($this->queue as $user) {
	        $email = $this->createEmail($user);
	        $this->emailVerifier->sendEmail('talav_user_registration_user_verify', $user, $email);
//            $this->emailVerifier->sendEmailConfirmation($user);
        }

        unset($this->queue); // calls gc
        $this->queue = [];
    }

//    public function getSubscribedEvents(): array
//    {
//        return [
//            Events::postFlush,
//        ];
//    }

    private function handleUserEmail(UserInterface $user): void
    {
        if ($user->isNewUser()) {
            return;
        }

        $user->setVerified(false);
        $this->queue[] = $user;
    }

	private function createEmail(UserInterface $user): RegistrationEmail
	{
		return (new RegistrationEmail())
//			->subject($this->translator->trans('registration.subject'))
			->subject('Confirm E-Mail')
			->from(new Address($this->fromEmail, $this->fromName))
			->to((string) $user->getEmail())
			->update(Importance::MEDIUM, $this->translator);
	}
}
