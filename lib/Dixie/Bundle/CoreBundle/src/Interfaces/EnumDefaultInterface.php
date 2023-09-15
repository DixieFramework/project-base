<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Interfaces;

/**
 * Interface to get the default enumeration value.
 *
 * @template T of \UnitEnum&EnumDefaultInterface
 */
interface EnumDefaultInterface
{
    /**
     * The default attribute name.
     */
    public const NAME = 'default';

    /**
     * Gets the default enumeration.
     *
     * @return T
     *
     * @throws \LogicException if default enumeration is not found
     */
    public static function getDefault(): self;

    /**
     * Returns if this enumeration is the default value.
     */
    public function isDefault(): bool;
}
