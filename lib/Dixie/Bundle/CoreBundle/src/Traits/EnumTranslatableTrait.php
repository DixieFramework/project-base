<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Elao\Enum\ReadableEnumTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait for enumeration implementing {@link \App\Interfaces\EnumTranslatableInterface EnumTranslatableInterface} interface.
 *
 * @psalm-require-implements \App\Interfaces\EnumTranslatableInterface
 */
trait EnumTranslatableTrait
{
    use ReadableEnumTrait;

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        return $translator->trans(id: $this->getReadable(), locale: $locale);
    }
}
