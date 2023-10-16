<?php

declare(strict_types=1);

namespace Talav\UserBundle\Form\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Talav\Component\User\Repository\UserRepositoryInterface;

final class DisableFieldsOnUserEdit implements EventSubscriberInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly Security $security)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event): void
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user || null === $user->getId()) {
            return;
        }

        if ($this->security->isGranted('edit_username', $this->userRepository->find($user->getId()))) {
            return;
        }

        $field = $form->get('username');
        $attrs = $field->getConfig()->getOptions();
        $attrs['disabled'] = 'disabled';

        $form->remove($field->getName());
        $form->add(
            $field->getName(),
            \get_class($field->getConfig()->getType()->getInnerType()),
            $attrs
        );
    }
}
