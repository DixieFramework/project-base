<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Talav\GalleryBundle\Entity\GalleryImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Talav\ImageBundle\Form\EventListener\ImageListener;
use Talav\ImageBundle\Service\ImageManager;

/**
 * Class ImageType.
 */
class GalleryImageType extends AbstractType
{
	public function __construct(
		private readonly ImageListener $imageListener
	) {
	}

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
        $builder->add('title', TextType::class, [
			'label' => 'label.title',
			'required' => true,
			'attr' => ['max_length' => 255],
        ]);

        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'label.description',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );

		$builder->add('image', FileType::class, [
			'required' => true,
			'constraints' => [
				new ImageConstraint([
					'detectCorrupted' => true,
					'groups' => ['upload'],
					'maxSize' => '10M',
					'mimeTypes' => ImageManager::IMAGE_MIMETYPES,
				])
			],
			'mapped' => false,
		]);

	    $builder->addEventSubscriber($this->imageListener->setFieldName('image'));
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
			'data_class' => GalleryImage::class,
	        // enable/disable CSRF protection for this form
			'csrf_protection' => true,
	        // the name of the hidden HTML field that stores the token
			'csrf_field_name' => '_csrf_token',
	        // an arbitrary string used to generate the value of the token
	        // using a different string for each form improves its security
			'csrf_token_id'   => 'gallery-image',
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
//    public function getBlockPrefix(): string
//    {
//        return 'image';
//    }
}
