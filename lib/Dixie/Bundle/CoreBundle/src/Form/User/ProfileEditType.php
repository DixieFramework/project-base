<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\OptionsResolver\OptionsResolver;
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
        parent::__construct(\Talav\UserBundle\Entity\User::class);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    protected function addFormFields(FormHelper $helper): void
    {
        $helper->domain('TalavUserBundle');

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

//        $helper->field('firstName')
//            ->label('user.fields.firstName')
//            ->addTextType();
//
//        $helper->field('lastName')
//            ->label('user.fields.lastName')
//            ->addTextType();
//
//        // image
//        $helper->field('imageFile')
//            ->updateOption('delete_label', 'user.edit.delete_image')
//            ->addVichImageType();

        // id for ajax validation
        $helper->field('id')
            ->notMapped()
            ->addHiddenType();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'TalavUserBundle'
        ]);
    }
}
