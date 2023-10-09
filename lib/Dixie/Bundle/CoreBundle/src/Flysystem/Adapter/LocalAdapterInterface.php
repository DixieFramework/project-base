<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Flysystem\Adapter;

interface LocalAdapterInterface extends ExtendedAdapterInterface
{
    public function getLocalPath(string $path): string;
}
