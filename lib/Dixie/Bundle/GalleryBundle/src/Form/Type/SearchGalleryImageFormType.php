<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\GalleryBundle\Entity\GalleryImage;

class SearchGalleryImageFormType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array<string, mixed> $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
	    $builder
		    ->setMethod('GET')
		    ->add('keywords', TextType::class, [
			    'label' => 'search.form.',
			    'required' => false,
		    ])
		    ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData'])
		    ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit'])
	    ;

	    //$this->addAgeAndGenderSelects($builder);
    }

	protected function addAgeAndGenderSelects(FormBuilderInterface $formBuilder)
	{
		$minAgeArray = [];
		for ($i = 18; $i <= 120; $i += 2) {
			$minAgeArray[$i] = $i;
		}
		$maxAgeArray = [];
		for ($i = 18; $i <= 120; $i += 2) {
			$maxAgeArray[$i] = $i;
		}
		$formBuilder
			->add('min_age', ChoiceType::class, [
				'choices' => $minAgeArray,
				'choice_translation_domain' => false,
				'label' => 'findpeopleminimumage',
				'translation_domain' => 'messages',
			])
			->add('max_age', ChoiceType::class, [
				'choices' => $maxAgeArray,
				'choice_translation_domain' => false,
				'label' => 'findpeoplemaximumage',
				'translation_domain' => 'messages',
			])
			->add('gender', ChoiceType::class, [
				'choices' => [
					'any' => null,
					'male' => 1,
					'female' => 2,
					'other' => 4,
				],
				'label' => 'gender',
				'required' => true,
				'translation_domain' => 'messages',
			]);
	}

	/**
	 * Add 'see map' option in case the map shows result from a zoom/pan operation or
	 * an empty search location (\todo).
	 *
	 * @throws AlreadySubmittedException
	 * @throws LogicException
	 * @throws UnexpectedTypeException
	 */
	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();
		$options = $form->getConfig()->getOptions();

		if (null !== $options['search_options'] && '' !== $options['search_options']) {
			$form
				->add('resetOptions', SubmitType::class, [
					'label' => 'search.options.reset',
					'attr' => [
						'class' => 'o-button o-button--outline mr-1',
					]
				])
			;
		}
	}

	public function preSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		if (!$form->has('resetOptions')) {
			$form
				->add('resetOptions', SubmitType::class, [
					'label' => 'search.options.reset',
					'attr' => [
						'class' => 'o-button o-button--outline mr-1',
					]
				])
			;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'search_options' => null,
			'translation_domain' => 'TalavGalleryBundle',
			'allow_extra_fields' => true,
            'block_prefix' => 'lol'
		]);
	}

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string Prefix
     */
    public function getBlockPrefix(): string
    {
        return '';
    }

	protected function getLabelPrefix(): ?string
	{
		return 'search.fields.';
	}
}
