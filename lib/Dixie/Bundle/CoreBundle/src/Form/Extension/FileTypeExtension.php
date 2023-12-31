<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Extends the FileType to use within the FileInput plugin.
 *
 * @extends AbstractFileTypeExtension<FileType>
 */
class FileTypeExtension extends AbstractFileTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [FileType::class];
    }

    /**
     * @psalm-param FormInterface<FileType> $form
     * @psalm-param array<array-key, mixed> $attributes
     * @psalm-param array<array-key, mixed> $options
     */
    protected function updateAttributes(FormInterface $form, array &$attributes, array &$options): void
    {
        // merge options from parent (VichFileType or VichImageType)
        if (($parent = $form->getParent()) instanceof FormInterface) {
            $configuration = $parent->getConfig();
            foreach (['placeholder', 'maxfiles', 'maxsize'] as $name) {
                $this->updateOption($configuration, $options, $name);
            }
        }

        // default
        parent::updateAttributes($form, $attributes, $options);
    }

    /**
     * Update an option.
     */
    private function updateOption(FormConfigInterface $configuration, array &$options, string $name): void
    {
        if ($configuration->hasOption($name)) {
            /** @psalm-var string $value */
            $value = $configuration->getOption($name);
            $options[$name] = $value;
        }
    }
}
