<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@TalavWeb/components/flash.html.twig')]
final class WebFlashMessage
{
}
