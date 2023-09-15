<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Form\Extension;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Extends VichImageType to use within the FileInput plugin.
 *
 * @extends AbstractFileTypeExtension<VichImageType>
 */
class VichImageTypeExtension extends AbstractFileTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('placeholder', null);
    }

    public static function getExtendedTypes(): iterable
    {
        return [VichImageType::class];
    }
}
