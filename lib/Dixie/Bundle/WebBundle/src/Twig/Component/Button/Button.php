<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Button.
 */
#[AsTwigComponent]
abstract class Button
{
    public ?string $name = null;
    public string $color = 'white';
    public string $variant = 'light';
    public string $size = 'sm';

    public ?string $icon = null;
    public string $iconPosition = 'left';

    public bool $rounded = false;
    public bool $outlined = false;
    public bool $dimmed = false;
    public bool $iconOnly = false;
    public bool $trigger = false;
}
