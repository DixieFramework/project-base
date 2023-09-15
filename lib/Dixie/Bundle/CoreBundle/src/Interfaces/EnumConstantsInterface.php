<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Interfaces;

/**
 * Interface to get the constant enumerations.
 */
interface EnumConstantsInterface
{
    /**
     * Gets the constant enumerations.
     *
     * @return array<string, mixed>
     */
    public static function constants(): array;
}
