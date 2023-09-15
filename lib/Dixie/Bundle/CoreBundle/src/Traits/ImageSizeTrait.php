<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Traits;

/**
 * Trait ot get image size.
 */
trait ImageSizeTrait
{
    /**
     * Gets the image size for the given file name.
     *
     * @return array{0: int, 1: int} the image size with width and height, if success; an empty array ([0, 0]) if fail
     */
    public function getImageSize(string $filename): array
    {
        if ('' === $filename) {
            return [0, 0];
        }

        /** @psalm-var int[]|false $size */
        $size = \getimagesize($filename);
        if (\is_array($size)) {
            return [$size[0], $size[1]];
        }

        return [0, 0];
    }
}
