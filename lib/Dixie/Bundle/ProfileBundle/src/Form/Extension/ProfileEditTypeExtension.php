<?php

declare(strict_types = 1);

namespace Talav\ProfileBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\ProfileBundle\Enum\Gender;
use Talav\ProfileBundle\Validator\Constraints\AgeVerification;
use Talav\CoreBundle\Form\User\ProfileEditType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class ProfileEditTypeExtension extends AbstractTypeExtension implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * CustomerProfileTypeExtension constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, private readonly TranslatorInterface $translator)
    {
        $this->setContainer($container);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'label' => 'talav.profile.fields.firstName',
                'translation_domain' => 'TalavProfileBundle',
                'required' => true,
                'property_path' => 'profile.firstName',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'talav.profile.form.first_name.blank'
                    ]),
                    new Constraints\Length([
                        'minMessage' => 'profile.first_name_invalid_length',
                        'maxMessage' => 'profile.first_name_invalid_length',
                        'min' => 2,
                        'max' => 32,
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'talav.profile.fields.lastName',
                'translation_domain' => 'TalavProfileBundle',
                'required' => true,
                'property_path' => 'profile.lastName',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'talav.profile.form.last_name.blank'
                    ]),
                    new Constraints\Length([
                        'minMessage' => 'profile.last_name_invalid_length',
                        'maxMessage' => 'profile.last_name_invalid_length',
                        'min' => 2,
                        'max' => 32,
                    ]),
                ],
            ])
            ->add('gender', EnumType::class, [
                'class' => Gender::class,
                'required' => true,
                'property_path' => 'profile.gender',
                'label' => 'label.gender',
                'translation_domain' => 'enums',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'choice_label' => function(Gender $gender) {
                    return $this->translator->trans('gender.' . $gender->value, [], 'enums');
                },
                'constraints' => [
                    new Constraints\Choice([
                        'message' => 'birthday.required',
                        'callback' => Gender::list()
                    ])
                ]
            ])
        ;

        $builder->remove('birthday');

        $builder
            ->add('birthday', BirthdayType::class, [
                'label' => 'talav.profile.label.birthdate',
                'translation_domain' => 'TalavProfileBundle',
                'widget' => 'single_text',
                'required' => true,
                'years' => range(date('Y') - 100, date('Y') - 18),
                'property_path' => 'profile.birthdate',
                'constraints' => [
                    new Constraints\NotBlank([
                        'groups' => ['talav']
                    ]),
                    new AgeVerification([
                        'age' => 18,//$this->container->getParameter('age_verification.minimal_age'),
                        'groups' => ['talav']
                    ]),
                ]
            ]);
    }

    /**
     * @return string
     */
    public static function getExtendedTypes(): iterable
    {
        return [ProfileEditType::class];
    }
}
