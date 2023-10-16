<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class DeleteButton.
 */
#[AsTwigComponent(template: '@TalavWeb/components/button/content_modal.html.twig')]
final class TalavContentModalButton
{
    public string $name;
    public string $id;
    public ?string $url = null;
    public ?string $action = null;
    public ?string $icon = null;
    public ?string $path = null;
}
