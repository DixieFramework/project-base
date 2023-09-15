<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * An abstract choice type.
 *
 * Subclass must override the <code>getChoices()</code> function.
 *
 * @extends AbstractType<ChoiceType>
 */
abstract class AbstractChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('choices', $this->getChoices());
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }

    /**
     * Gets the choices array.
     *
     * @return array an array, where the array key is the item's label and the array value is the item's value
     */
    abstract protected function getChoices(): array;
}