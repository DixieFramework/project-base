<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class OverviewCard.
 */
#[AsTwigComponent(template: '@TalavWeb/components/card/overview.html.twig')]
class TalavOverviewCard
{
    public string $label;
    public ?float $value = null;
    public ?float $ratio = null;
    public ?string $chart = null;
    public ?string $info = 'Aucun changement';
}
