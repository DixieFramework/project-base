<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DataTable\Adapter;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\CoreBundle\DataTable\DTO\DataTableResult;
use Talav\CoreBundle\DataTable\DTO\DataTableState;

class CallableAdapterType extends AdapterType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('callable')
            ->setAllowedTypes('callable', 'callable');
    }

    public function getResult(DataTableState $state, array $options): DataTableResult
    {
        return call_user_func($options['callable'], $state);
    }
}
