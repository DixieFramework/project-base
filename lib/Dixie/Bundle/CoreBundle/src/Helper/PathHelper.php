<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Helper;

class PathHelper
{
    public static function join(string ...$parts): string
    {
        return preg_replace(
            pattern: '~[/\\\\]+~',
            replacement: \DIRECTORY_SEPARATOR,
            subject: implode(\DIRECTORY_SEPARATOR, $parts)
        ) ?: '';
    }
}
