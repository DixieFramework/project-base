<?php

declare(strict_types=1);

namespace Talav\WebBundle\Engine\Asset;

interface MergerInterface
{
    /**
     * Merge the assets, publish them and return list of output files.
     */
    public function merge(array $assets, $type = 'js'): array;
}
