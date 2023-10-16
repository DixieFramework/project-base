<?php

declare(strict_types=1);

namespace Talav\UserBundle\Doctrine\EntityListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Enums\Importance;
use Talav\CoreBundle\Helper\MailerHelper;
use Talav\CoreBundle\Mime\RegistrationEmail;
use Talav\CoreBundle\Service\EmailVerifier;
use Talav\UserBundle\Entity\User;
use Talav\UserBundle\Event\TalavUserEvents;
use Talav\UserBundle\Event\UserEvent;
use Talav\UserBundle\Event\UserFormEvent;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
#[AsDoctrineListener(event: Events::postFlush)]
//#[AsEntityListener(event: Events::postFlush, method: 'postFlush', entity: User::class)]
class UserEntityListener implements EventSubscriberInterface
{
	/** @var User[] */
	private array $queue = [];

    public function __construct(
        private EmailVerifier $emailVerifier,
		private readonly EventDispatcherInterface $eventDispatcher,
		private readonly MailerHelper $mailer,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
	    private readonly TranslatorInterface $translator,
	    #[Autowire('%mailer_user_name%')]
	    private readonly string $fromName,
	    #[Autowire('%mailer_user_email%')]
	    private readonly string $fromEmail,
    ) {
    }

	public function prePersist(UserInterface $user): void
	{
		$this->handleUserEmail($user);
	}

    public function preUpdate(UserInterface $user, PreUpdateEventArgs $eventArgs): void
    {
	    if (!$eventArgs->hasChangedField('email')) {
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
			$event = new UserEvent($user);
			$this->eventDispatcher->dispatch($event, TalavUserEvents::PROFILE_EDIT_SUCCESS);

//			$email = $this->createEmail($user);
//			$this->emailVerifier->sendEmail('talav_user_registration_user_verify', $user, $email);
		}

		unset($this->queue); // calls gc
		$this->queue = [];
	}

	private function handleUserEmail(UserInterface $user): void
	{
		if ($user->isNewUser()) {
			return;
		}

		$user->setVerified(false);
		$this->queue[] = $user;
	}

	public function getSubscribedEvents(): array
	{
		return [
			Events::postFlush,
		];
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
