<?php

declare(strict_types=1);

namespace Talav\UserBundle\Doctrine\EntityListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Service\EmailVerifier;

class UserEntityListener
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private TokenStorageInterface $tokenStorage,
        private FlashBagInterface $flashBag
    ) {
    }

    public function preUpdate(UserInterface $user, PreUpdateEventArgs $eventArgs): void
    {
        /** @var UserInterface $editedUser */
        $editedUser = $eventArgs->getObject();

        if ($eventArgs->hasChangedField('email')) {
            $editedUser->setVerified(false);

            $this->emailVerifier->sendEmailConfirmation(
                'user',
                $editedUser,
                (new TemplatedEmail())
                    ->from(new Address('hello@mon-petit-producteur.fr', 'Mon Petit Producteur'))
                    ->to(new Address($editedUser->getEmail(), $editedUser->getFullName()))
                    ->subject('Veuillez confirmer votre adresse Email')
                    ->htmlTemplate('ui/registration/confirmation_email.html.twig')
            );

            $this->flashBag->add(
                'info',
                'Un email viens de vous être envoyé sur votre nouvelle adresse afin de réactiver votre compte.'
            );

            $this->tokenStorage->setToken();
        }
    }
}
