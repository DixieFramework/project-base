<?php

declare(strict_types=1);

namespace Talav\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search_string', TextType::class, [
				'label' => 'search.fields.query',
                'required' => true,
                'attr' => [
                    'placeholder' => 'search.placeholder'
                ]
            ])
	        ->add('submit', SubmitType::class, [
                'label' => 'Search',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
			'translation_domain' => 'TalavWebBundle'
        ]);
    }
}
