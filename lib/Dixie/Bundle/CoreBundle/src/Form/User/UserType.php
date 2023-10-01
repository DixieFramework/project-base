<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Talav\UserBundle\Entity\User;
use Talav\CoreBundle\Form\AbstractEntityType;
use Talav\CoreBundle\Form\FormHelper;
use Talav\CoreBundle\Utils\FormatUtils;
use Symfony\Component\Form\Event\PreSetDataEvent;

/**
 * User edit type.
 *
 * @template-extends AbstractEntityType<User>
 */
class UserType extends AbstractEntityType
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(User::class);
    }

    protected function addFormFields(FormHelper $helper): void
    {
        $helper->field('id')
            ->addHiddenType();

        $helper->field('username')
            ->addUserNameType(false);

        $helper->field('email')
            ->addEmailType();

        $helper->field('plainPassword')
            ->addRepeatPasswordType();

//        $helper->field('role')
//            ->add(RoleChoiceType::class);

        $helper->field('enabled')
            ->addTrueFalseType('common.value_enabled', 'common.value_disabled');

        $helper->field('lastLogin')
            ->updateOption('value_transformer', $this->formatLastLogin(...))
            ->updateOption('empty_value', 'common.value_none')
            ->widgetClass('text-center')
            ->addPlainType(true);

//        $helper->field('imageFile')
//            ->updateOption('maxsize', '10mi')
//            ->addVichImageType();

        $helper->listenerPreSetData($this->onPreSetData(...));
    }

    /**
     * Format the last login date.
     */
    private function formatLastLogin(\DateTimeInterface|string $lastLogin): ?string
    {
        if ($lastLogin instanceof \DateTimeInterface) {
            return FormatUtils::formatDateTime($lastLogin);
        }

        return null;
    }

    /**
     * Handles the preset data event.
     */
    private function onPreSetData(PreSetDataEvent $event): void
    {
        /** @var User $user */
        $user = $event->getData();
        $form = $event->getForm();
        //if ($user->isNew())
        if (true)
        {
            $form->remove('lastLogin');
        } else {
            $form->remove('plainPassword');
        }
    }
}
