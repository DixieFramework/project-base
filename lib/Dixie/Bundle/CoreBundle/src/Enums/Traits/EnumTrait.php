<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums\Traits;

trait EnumTrait
{
    public static function fromName(string $name): ?static
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}