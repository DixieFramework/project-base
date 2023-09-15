<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Interfaces;

/**
 * Interface to get sorted enumerations.
 *
 * @template T of \UnitEnum&EnumSortableInterface
 */
interface EnumSortableInterface
{
    /**
     * Gets the sorted enumerations.
     *
     * @return T[]
     */
    public static function sorted(): array;
}
