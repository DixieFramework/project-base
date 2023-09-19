<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Enums\Traits;

use function array_column;

trait GetValuesTrait
{
    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
