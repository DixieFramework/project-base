<?php

declare(strict_types=1);

namespace Talav\CoreBundle\DataTable\Adapter;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Talav\CoreBundle\DataTable\AdapterException;
use Talav\CoreBundle\DataTable\DTO\DataTableResult;
use Talav\CoreBundle\DataTable\DTO\DataTableState;

abstract class AdapterType
{
    public function configureOptions(OptionsResolver $resolver)
    {
    }

    /**
     * @throws AdapterException
     */
    abstract public function getResult(DataTableState $state, array $options): DataTableResult;
}
