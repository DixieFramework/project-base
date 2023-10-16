<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@TalavWeb/components/layout/footer.html.twig')]
final class TalavFooter
{
    public string $copyRight;
}
