<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Modal.
 */
#[AsTwigComponent(template: '@TalavWeb/components/card/content_modal.html.twig')]
final class TalavContentModal
{
    public string $id;
    public string $url;
    public ?string $action = null;
    public string $position = 'bottom';
    public string $size = 'lg';
}
