<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class TalavAlert
{
    public string $type = 'icon';
    public string $message;
    public bool $dismissible = false;
    public string $state = 'danger';
    public string $fill = '';
    public string $icon = 'info';
}
