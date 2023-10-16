<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class BorderedTable.
 */
#[AsTwigComponent(template: '@TalavWeb/components/table/bordered.html.twig')]
final class TalavBorderedTable
{
    public mixed $data;
}
