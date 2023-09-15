<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Interfaces;

use Elao\Enum\ReadableEnumInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Interface to get the translatable enumeration value.
 */
interface EnumTranslatableInterface extends ReadableEnumInterface, TranslatableInterface
{
}
