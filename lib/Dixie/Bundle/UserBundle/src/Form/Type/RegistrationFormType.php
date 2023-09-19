<?php

declare(strict_types=1);

namespace Talav\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;
use Talav\CoreBundle\Validator\LettersAndNumbers;
use Talav\UserBundle\Validator\Constraints\RegisteredUser;

final class RegistrationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'talav.form.email',
                'translation_domain' => 'TalavUserBundle'
            ]);

        $builder
            ->add('username',TextType::class, [
                'label' => 'profile.username_no_spaces',
                'translation_domain' => 'TalavUserBundle',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'profile.blank_username',
                    ]),
                    new LettersAndNumbers([
                        'message' => 'profile.username_letters_and_numbers',
                    ]),
                    new RegisteredUser([
                        'message' => 'profile.unique_username',
                        'field' => 'username'
                    ]),
                    new Constraints\Length([
                        'minMessage' => 'profile.username_invalid_length',
                        'maxMessage' => 'profile.username_invalid_length',
                        'min' => 3,
                        'max' => 32,
                    ]),
                ],
            ]
        );
//            ->add('username', null, ['label' => 'talav.form.username', 'translation_domain' => 'TalavUserBundle'])

        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'translation_domain' => 'TalavUserBundle',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => ['label' => 'talav.form.password'],
                'second_options' => ['label' => 'talav.form.password_confirmation'],
                'invalid_message' => 'talav.password.mismatch',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'talav_user_registration';
    }
}
