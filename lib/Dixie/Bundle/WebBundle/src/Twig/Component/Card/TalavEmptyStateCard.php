<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class EmptyStateCard.
 */
#[AsTwigComponent(template: '@TalavWeb/components/card/empty_state.html.twig')]
final class TalavEmptyStateCard
{
    public ?string $action = null;
    public ?string $path = null;
    public ?string $label = null;
}
