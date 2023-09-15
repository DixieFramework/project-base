<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Talav\CoreBundle\Interfaces\EnumDefaultInterface;

/**
 * Trait for enumeration implementing {@link EnumDefaultInterface} interface.
 *
 * @psalm-require-implements EnumDefaultInterface
 */
trait EnumDefaultTrait
{
    use EnumExtrasTrait;

    public static function getDefault(): self
    {
        /** @var self[] $values */
        $values = static::cases();
        foreach ($values as $value) {
            if ($value->isDefault()) {
                return $value;
            }
        }

        throw new \LogicException('Unable to find the default value.');
    }

    public function isDefault(): bool
    {
        return $this->getExtraBool(EnumDefaultInterface::NAME);
    }
}
