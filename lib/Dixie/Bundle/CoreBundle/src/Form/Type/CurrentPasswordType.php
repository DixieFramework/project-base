<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\Type;

use Talav\CoreBundle\Interfaces\EntityInterface;
use Talav\CoreBundle\Interfaces\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Type for the current user password.
 *
 * @extends AbstractType<PasswordType>
 */
class CurrentPasswordType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'mapped' => false,
            'label' => 'user.password.current',
            'autocomplete' => 'current-password',
            'attr' => [
                'minLength' => UserInterface::MIN_PASSWORD_LENGTH,
                'maxLength' => EntityInterface::MAX_STRING_LENGTH,
            ],
            'constraints' => [
                new NotBlank(),
                new Length(min: UserInterface::MIN_PASSWORD_LENGTH, max: EntityInterface::MAX_STRING_LENGTH),
                new UserPassword(message: 'current_password.invalid'),
            ],
        ]);
    }

    public function getParent(): ?string
    {
        return PasswordType::class;
    }
}
