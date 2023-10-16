<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class DeleteButton.
 */
#[AsTwigComponent(template: '@TalavWeb/components/button/delete.html.twig')]
final class TalavDeleteButton
{
    public string $path;
    public ?string $redirect = null;
    public string $type = 'icon';
    public string $remove = '.nk-tb-item';
    public string $id;
}
