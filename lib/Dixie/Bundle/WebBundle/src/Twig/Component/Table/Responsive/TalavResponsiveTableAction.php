<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Table\Responsive;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Talav\WebBundle\Twig\Component\Button\Button;

/**
 * Class ResponsiveTableAction.
 */
#[AsTwigComponent(template: '@TalavWeb/components/table/responsive/action.html.twig')]
final class TalavResponsiveTableAction extends Button
{
    public string $path;
    public bool $hidden = true;
    public bool $trigger = true;
    public bool $iconOnly = true;
    public string $color = 'transparent';
}
