<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Card;

/**
 * Class DashliteHistogramOverviewCard.
 */
final class TalavHistogramOverviewCard
{
    public ?string $label = null;
    public ?string $description = null;
    public ?string $tooltip = null;
    public array $month = [];
    public array $week = [];
    public ?string $chart = null;
}
