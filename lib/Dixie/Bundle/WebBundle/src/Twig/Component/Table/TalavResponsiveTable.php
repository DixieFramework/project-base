<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class ResponsiveTable.
 */
#[AsTwigComponent(template: '@TalavWeb/components/table/responsive.html.twig')]
final class TalavResponsiveTable
{
    public mixed $data;
    public ?string $emptyStatePath = null;
    public ?string $emptyStateAction = null;
    public bool $separated = true;
    public bool $disablePagination = false;
}
