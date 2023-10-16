<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Icon.
 */
#[AsTwigComponent(template: '@TalavWeb/components/icon.html.twig')]
final class TalavIcon
{
    public string $name;
    public string $color = '';
}
