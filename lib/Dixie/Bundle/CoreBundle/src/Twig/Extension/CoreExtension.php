<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Extension;

use Talav\CoreBundle\Twig\Runtime\CoreExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CoreExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [CoreExtensionRuntime::class, 'displayIcon']),
        ];
    }
}
