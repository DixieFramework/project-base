<?php

declare(strict_types=1);

namespace Talav\GalleryBundle\Form\Type;

use Symfony\Component\Validator\Constraints;
use Talav\GalleryBundle\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GalleryType.
 */
class GalleryType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name',  TextType::class, [
            'label' => 'talav.gallery.fields.name',
            'required' => true,
            'attr' => ['max_length' => 64],
            'error_bubbling' => true,
            'constraints' => [
                new Constraints\Regex([
                    'pattern' => '/^[A-Za-z0-9-éèàêï\s]{2,64}$/i',
                    'message' => 'Gallery name must only contain letters and/or numbers, with a length of 2 to 64 characters.'
                ]),
                new Constraints\NotBlank([
                    'message' => 'What name would you like to give to your gallery ?'
                ]),
                new Constraints\Length([
                    'min' => 2,
                    'max' => 64,
                    'minMessage' => 'Gallery name must contain at least {{ limit }} characters',
                    'maxMessage' => 'Gallery name must contain at most {{ limit }} characters',
                ]),
            ],
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
			'data_class' => Gallery::class,
	        'translation_domain' => 'TalavGalleryBundle'
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
        return 'gallery';
    }
}
