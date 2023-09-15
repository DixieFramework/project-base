<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Talav\CoreBundle\Entity\User;
use Talav\CoreBundle\Form\AbstractEntityType;
use Talav\CoreBundle\Form\FormHelper;

/**
 * Type to update the user profile.
 *
 * @template-extends AbstractEntityType<User>
 */
class ProfileEditType extends AbstractEntityType
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    protected function addFormFields(FormHelper $helper): void
    {
        // username
        $helper->field('username')
            ->addUserNameType();

        // email
        $helper->field('email')
            ->autocomplete('email')
            ->addEmailType();

        // current password
        $helper->field('currentPassword')
            ->label('user.password.current')
            ->addCurrentPasswordType();

        // image
        $helper->field('imageFile')
            ->updateOption('delete_label', 'user.edit.delete_image')
            ->addVichImageType();

        // id for ajax validation
        $helper->field('id')
            ->addHiddenType();
    }
}
