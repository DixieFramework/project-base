<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Table\Responsive;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class ResponsiveTableColumn.
 */
#[AsTwigComponent(template: '@TalavWeb/components/table/responsive/column.html.twig')]
final class TalavResponsiveTableColumn
{
    public ?string $value = null;
    public ?string $responsive = null;
    public bool $tools = false;
    public bool $col = false;
}
