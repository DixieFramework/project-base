<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Talav\CoreBundle\Form\FormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Type to reset the user password.
 *
 * @extends AbstractType<\Symfony\Component\Form\FormTypeInterface>
 */
class ResetChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $helper = new FormHelper($builder);
        $helper->field('plainPassword')
            ->addRepeatPasswordType('user.password.new', 'user.password.new_confirmation');
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
