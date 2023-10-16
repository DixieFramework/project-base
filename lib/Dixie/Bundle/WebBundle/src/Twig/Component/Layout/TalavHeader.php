<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Header.
 */
#[AsTwigComponent(template: '@TalavWeb/components/layout/header.html.twig')]
final class TalavHeader
{
    public ?string $title = null;
    public mixed $data;
    public ?string $backPath = null;
    public ?string $backTitle = null;
}
