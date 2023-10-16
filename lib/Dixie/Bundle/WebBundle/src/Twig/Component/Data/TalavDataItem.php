<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Data;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@TalavWeb/components/data/item.html.twig')]
final class TalavDataItem
{
    public string $style = 'wider';
    public string $label;
    public ?string $value;
    public bool $link = false;
}
