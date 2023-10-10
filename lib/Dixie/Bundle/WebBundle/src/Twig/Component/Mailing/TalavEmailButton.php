<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Mailing;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
#[AsTwigComponent(template: '@TalavWeb/components/mailing/button.html.twig')]
final class TalavEmailButton
{
    public ?string $url = null;
    public ?string $title = null;
    public string $color = 'primary';
    public string $format = 'html';
}
