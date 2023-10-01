<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\Type;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @implements DataTransformerInterface<mixed, mixed>
 */
class MonthYearType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'placeholder' => 'MM/YYYY',
            ],
            'invalid_message' => 'Invalid month or year',
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function transform(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('m/Y');
        }

        return null;
    }

    public function reverseTransform(mixed $value)
    {
        if (! $value) {
            return null;
        }

        return DateTimeImmutable::createFromFormat('m/Y', $value);
    }
}
