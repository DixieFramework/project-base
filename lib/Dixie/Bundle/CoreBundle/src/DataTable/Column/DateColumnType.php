<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DataTable\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateColumnType extends PropertyColumnType
{
    public function renderProperty($value, array $options): string
    {
        return $value instanceof \DateTimeInterface ? $value->format($options['format']) : (string) $value;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('format', 'd/m/Y')
            ->setAllowedTypes('format', 'string');
    }
}
