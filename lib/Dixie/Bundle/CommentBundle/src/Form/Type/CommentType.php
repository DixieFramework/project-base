<?php

declare(strict_types=1);

namespace Talav\CommentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Talav\CommentBundle\Entity\CommentInterface;
use Talav\PostBundle\Entity\Comment;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextareaType::class,[
                'attr' => [
                    'class' => 'md-auto-sizer',
                    'placeholder' => 'write.comment',
                    'rows' => 1
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('replyTo', HiddenType::class, [
                'mapped' => false
            ])
            ->add('replyFor', HiddenType::class, [
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommentInterface::class,
        ]);
    }
}
