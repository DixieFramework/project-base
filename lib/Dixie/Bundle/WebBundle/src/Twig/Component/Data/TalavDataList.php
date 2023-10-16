<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Data;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@TalavWeb/components/data/list.html.twig')]
final class TalavDataList
{
    public string $title = '';
    public string $description = '';
    public string $value;
}
