<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Runtime;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

class CoreExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * @param list<string> $options
     */
    public function displayIcon(string $iconName, array $options = []): string
    {
        $class = sprintf('fa fa-%s', $iconName);

        if ([] !== $options) {
            $class .= ' ' . implode(' ', $options);
        }

        return sprintf('<i class="%s"></i>', $class);
    }
}
