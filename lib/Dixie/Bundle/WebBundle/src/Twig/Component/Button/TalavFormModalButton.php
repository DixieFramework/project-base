<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class DeleteButton.
 */
#[AsTwigComponent(template: '@TalavWeb/components/button/form_modal.html.twig')]
final class TalavFormModalButton
{
    public string $name;
    public string $icon;
    public ?string $path;
}
