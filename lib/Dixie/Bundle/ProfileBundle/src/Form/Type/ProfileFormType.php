<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Talav\ProfileBundle\Enum\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\ProfileBundle\Validator\Constraints\AgeVerification;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileFormType extends AbstractType
{
    public function __construct(private string $class, private readonly ContainerInterface $container, private readonly TranslatorInterface $translator) { }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'label' => 'talav.profile.fields.firstName',
                'translation_domain' => 'TalavProfileBundle',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'talav.profile.form.first_name.blank'
                    ]),
                    new Constraints\Length([
                        'min' => 2,
                        'minMessage' => 'profile.first_name_invalid_length',
                        'max' => 32,
                        'maxMessage' => 'profile.first_name_invalid_length',
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'talav.profile.fields.lastName',
                'translation_domain' => 'TalavProfileBundle',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'talav.profile.form.last_name.blank'
                    ]),
                    new Constraints\Length([
                        'min' => 2,
                        'minMessage' => 'profile.last_name_invalid_length',
                        'max' => 32,
                        'maxMessage' => 'profile.last_name_invalid_length',
                    ]),
                ],
            ])
//            ->add('bio', TextareaType::class, [
//                'label' => 'ria.profile.fields.about',
//                'required' => false,
//                'attr' => [
//                    'style' => 'opacity:0;margin-bottom:20px',
//                    'class' => 'ckeditor',
//                    'rows' => 10
//                ],
//                'constraints' => [
//                    new Constraints\Length([
//                        'max' => 800,
//                        'maxMessage' => 'max.message'
//                    ])
//                ]
//            ])
            ->add('gender', EnumType::class, [
                'class' => Gender::class,
                'label' => 'label.gender',
                'translation_domain' => 'enums',
                'expanded' => false,
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choice_label' => function(Gender $gender) {
                    return $this->translator->trans('gender.' . $gender->value, [], 'enums');
                }
            ])
//            ->add('gender', ChoiceType::class, [
//                'label' => 'ria.profile.fields.gender.label',
//                'expanded' => true,
//                'label_attr' => ['class' => 'radio-custom'],
//                'choices' => [
//                    'ria.profile.fields.gender.male' => Gender::Male,
//                    'ria.profile.fields.gender.female' => Gender::Female
//                ],
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'gender.required.message'
//                    ])
//                ]
//            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'talav.profile.label.birthdate',
                'translation_domain' => 'TalavProfileBundle',
//                'widget' => 'single_text',
                'required' => true,
                'years' => range(date('Y') - 100, date('Y') - 18),
                'constraints' => [
                    new Constraints\NotBlank([
                        'groups' => ['talav']
                    ]),
                    new AgeVerification([
                        'age' => $this->container->getParameter('registration.age_verification.minimal_age'),
                        'groups' => ['talav']
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => $this->class,
            'translation_domain' => 'TalavProfileBundle',
            'csrf_token_id' => 'profile'
        ]);
    }
}
