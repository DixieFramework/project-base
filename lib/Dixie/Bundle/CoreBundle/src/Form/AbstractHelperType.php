<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Abstract type to use within the {@link FormHelper}.
 *
 * @extends AbstractType<mixed>
 */
abstract class AbstractHelperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $helper = $this->createFormHelper($builder);
        $this->addFormFields($helper);
    }

    /**
     * Adds the form fields within the given helper.
     */
    abstract protected function addFormFields(FormHelper $helper): void;

    /**
     * Creates the form helper.
     */
    protected function createFormHelper(FormBuilderInterface $builder): FormHelper
    {
        return new FormHelper($builder, $this->getLabelPrefix());
    }

    /**
     * Gets the label prefix.
     *
     * If the prefix is not null, the label is automatically added when the field property is set.
     */
    protected function getLabelPrefix(): ?string
    {
        return null;
    }
}
