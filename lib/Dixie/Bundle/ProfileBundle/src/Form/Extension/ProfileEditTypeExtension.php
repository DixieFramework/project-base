<?php

declare(strict_types = 1);

namespace Talav\ProfileBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Talav\CoreBundle\Form\Type\SimpleEditorType;
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
     * @var string
     */
    protected const PATTERN_FIRST_NAME = '/^[^:\/<>]+$/';

    /**
     * @var string
     */
    protected const PATTERN_LAST_NAME = '/^[^:\/<>]+$/';

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
                'property_path' => 'profile.gender',
                'label' => 'label.gender',
                'translation_domain' => 'enums',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'choice_label' => function(Gender $gender) {
                    return $this->translator->trans('gender.' . $gender->value, [], 'enums');
                }
            ])
//            ->add('gender', ChoiceType::class, [
//                'property_path' => 'profile.gender',
//                'label' => 'ria.profile.fields.gender.label',
//                'translation_domain' => 'enums',
//                'expanded' => true,
//                'label_attr' => ['class' => 'radio-custom'],
//                'choice_label' => function(Gender $gender) {
//                    return $this->translator->trans('gender.' . $gender->value, [], 'enums');
//                },
//                'constraints' => [
//                    new Constraints\NotBlank([
//                        'message' => 'gender.required.message'
//                    ])
//                ]
//            ])
        ;

        $builder->remove('birthday');

        $builder
            ->add('birthday', BirthdayType::class, [
                'label' => 'talav.profile.label.birthdate',
                'translation_domain' => 'TalavProfileBundle',
//                'widget' => 'single_text',
                'required' => true,
                'years' => range(date('Y') - 100, date('Y') - 18),
                'property_path' => 'profile.birthdate',
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

            ->add('bio', SimpleEditorType::class, [
                'property_path' => 'profile.bio',
                'label' => 'talav.profile.label.bio',
                'translation_domain' => 'TalavProfileBundle',
                'attr' => [
                    'minLength' => 10
                ]
            ])
        ;
    }

    /**
     * @return string
     */
    public static function getExtendedTypes(): iterable
    {
        return [ProfileEditType::class];
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    protected function createNotBlankConstraint(): Constraints\NotBlank
    {
        return new Constraints\NotBlank();
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createFirstNameRegexConstraint(): Constraints\Regex
    {
        return new Constraints\Regex([
            'pattern' => static::PATTERN_FIRST_NAME,
        ]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createLastNameRegexConstraint(): Constraints\Regex
    {
        return new Constraints\Regex([
            'pattern' => static::PATTERN_LAST_NAME,
        ]);
    }
}
