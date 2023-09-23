<?php

declare(strict_types=1);

namespace Talav\ProfileBundle\Form\Type;

use Talav\ProfileBundle\Entity\UserRelation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
class UserRelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextType::class, [
                'label' => 'talav.profile.relations.fields.comment',
                'property_path' => 'commentText',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => UserRelation::class,
	            'translation_domain' => 'TalavProfileBundle'
            ])
        ;
    }
}
