<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

use Elao\Enum\ExtrasTrait;

/**
 * Extends extras trait with typed values.
 */
trait EnumExtrasTrait
{
    use ExtrasTrait;

    public function getExtraBool(string $key, bool $default = false, bool $throwOnMissingExtra = false): bool
    {
        /** @psalm-var bool|null $value */
        $value = $this->getExtra($key, $throwOnMissingExtra);

        return \is_bool($value) ? $value : $default;
    }

    public function getExtraInt(string $key, int $default = 0, bool $throwOnMissingExtra = false): int
    {
        /** @psalm-var int|null $value */
        $value = $this->getExtra($key, $throwOnMissingExtra);

        return \is_int($value) ? $value : $default;
    }

    public function getExtraString(string $key, string $default = '', bool $throwOnMissingExtra = false): string
    {
        /** @psalm-var string|null $value */
        $value = $this->getExtra($key, $throwOnMissingExtra);

        return \is_string($value) ? $value : $default;
    }
}
