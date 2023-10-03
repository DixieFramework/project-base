<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Helper;

class PathHelper
{

    /**
     * Joins path segments to one full path.
     *
     * @param string[] ...$paths
     */
    public static function join (string ...$paths) : string
    {
        $normalized = [];

        foreach ($paths as $index => $path)
        {
            if (0 !== $index)
            {
                $path = \ltrim($path, "/");
            }

            if ($index !== \count($paths) - 1)
            {
                $path = \rtrim($path, "/");
            }

            $normalized[] = $path;
        }

        return preg_replace(
            pattern: '~[/\\\\]+~',
            replacement: \DIRECTORY_SEPARATOR,
            subject: implode(\DIRECTORY_SEPARATOR, $normalized)
        ) ?: '';
    }
}
