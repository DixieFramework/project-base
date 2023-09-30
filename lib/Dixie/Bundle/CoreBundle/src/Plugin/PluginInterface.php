<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Plugin;

interface PluginInterface
{
    public function getName(): string;

    public function getPath(): string;
}
