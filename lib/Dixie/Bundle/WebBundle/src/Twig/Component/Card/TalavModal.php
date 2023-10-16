<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Modal.
 */
#[AsTwigComponent(template: '@TalavWeb/components/card/modal.html.twig')]
final class TalavModal
{
    public string $action;
    public string $path;
    public string $position = 'top';
}
