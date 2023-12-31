<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\User;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\Component\User\Model\UserInterface;
use Talav\CoreBundle\Entity\User;
use Talav\CoreBundle\Form\AbstractEntityType;
use Talav\CoreBundle\Form\FormHelper;
use Talav\UserBundle\Form\EventListener\DisableFieldsOnUserEdit;

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
    public function __construct(private readonly DisableFieldsOnUserEdit $disableFieldsOnUserEdit)
    {
        parent::__construct(UserInterface::class);
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
            ->label(false)
            ->addUserNameFieldType();

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
//            ->notMapped()
            ->addHiddenType();

		$helper->getBuilder()->addEventSubscriber($this->disableFieldsOnUserEdit);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInterface::class,
            'translation_domain' => 'TalavUserBundle'
        ]);
    }
}
