<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Talav\ProfileBundle\Form\Model\ProfileFilterFormModel;
use Talav\ProfileBundle\Repository\AttributeCategoryRepository;
use Talav\WebBundle\Entity\Region;

class ProfileFilterFormType extends AbstractType
{
    private array $distances;
    private const INTERESTS = 'interests';
    private const REGIONS = 'regions';
    private AttributeCategoryRepository $categoryRepository;

    public function __construct(AttributeCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;

        $this->distances = [
                '500' => '500000',
                '250' => '250000',
                '100' => '100000',
                '75' => '75000',
                '50' => '50000',
                '25' => '25000'
            ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileFilterFormModel::class,
            self::REGIONS => [],
            self::INTERESTS => []
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'colors',
            ChoiceType::class,
            [
                'choices' => $this->categoryRepository->findOneBy(['name' => 'color'])->getAttributes(),
                'choice_label' => 'name',
                'choice_translation_domain' => 'attributes',
                'multiple' => true,
                'expanded' => true
            ]
        );

        $builder->add(
            'shapes',
            ChoiceType::class,
            [
                'choices' => $this->categoryRepository->findOneBy(['name' => 'shape'])->getAttributes(),
                'choice_label' => 'name',
                'choice_translation_domain' => 'attributes',
                'multiple' => true,
                'expanded' => true
            ]
        );

        $builder->add('region', EntityType::class, [
            'choices' => $options[self::REGIONS],
            'class' => Region::class,
            'choice_label' => 'name',
            'placeholder' => '',
            'required' => false
        ]);

        $builder->add('distance', ChoiceType::class, [
            'choices' => $this->distances,
            'choice_label' => function ($choice) {
                return $choice/1000 . " km";
            },
            'label' => 'profile.distance',
            'required' => true,
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add('minAge', ChoiceType::class, [
            'choices' => range(18, 100),
            'label' => 'profile.minimum_age',
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => true,
            'constraints' => [
                new NotBlank()
            ]
         ]);

        $builder->add('maxAge', ChoiceType::class, [
            'choices' => range(18, 100),
            'label' => 'profile.maximum_age',
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => true,
            'constraints' => [
                new NotBlank()
            ]
        ]);

        $builder->add(
            'interests',
            ChoiceType::class,
            [
                'choices' => $options[self::INTERESTS],
                'choice_label' => 'name',
                'choice_translation_domain' => 'interests',
                'choice_value' => 'id',
                'multiple' => true,
                'expanded' => true
            ]
        );

        $builder->add('save', SubmitType::class, [
            'label' => 'search.search'
        ]);
    }
}
