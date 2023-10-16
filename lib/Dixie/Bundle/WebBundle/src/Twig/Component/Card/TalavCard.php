<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Card.
 */
#[AsTwigComponent(template: '@TalavWeb/components/card/card.html.twig')]
final class TalavCard
{
    public string $title = '';
    public bool $removePadding = false;
}
