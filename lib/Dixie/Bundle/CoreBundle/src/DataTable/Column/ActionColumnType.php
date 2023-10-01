<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DataTable\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\CoreBundle\DataTable\ActionRenderer;
use Talav\CoreBundle\DataTable\DataTableFactory;

class ActionColumnType extends ColumnType
{
    public function __construct(protected DataTableFactory $factory, protected ActionRenderer $renderer)
    {
    }

    public function render($rowData, array $options): string
    {
        $builder = $this->factory->createColumnActionBuilder();
        call_user_func($options['build'], $builder, $rowData, $options);
        return $this->renderer->renderActions($builder->getActions());
    }

    public function isSafeHtml(): bool
    {
        return true;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('class', 'text-end text-nowrap')
            ->setDefault('label', null);

        $resolver
            ->setRequired('build')
            ->setAllowedTypes('build', 'callable');
    }
}
