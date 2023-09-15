<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Talav\CoreBundle\Entity\User;
use Talav\CoreBundle\Form\AbstractEntityType;
use Talav\CoreBundle\Form\FormHelper;

/**
 * Change user password type.
 *
 * @template-extends AbstractEntityType<User>
 */
class UserChangePasswordType extends AbstractEntityType
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
        $helper->field('username')
            ->updateOption('hidden_input', true)
            ->addPlainType(true);
        $helper->field('plainPassword')
            ->addRepeatPasswordType('user.password.new', 'user.password.new_confirmation');
    }
}
