<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class DeleteButton.
 */
#[AsTwigComponent(template: '@TalavWeb/components/button/link.html.twig')]
final class TalavLinkButton extends Button
{
    public string $path;
    public string $color = 'white';
    public string $size = 'sm';
    public string $iconPosition = 'left';
}
