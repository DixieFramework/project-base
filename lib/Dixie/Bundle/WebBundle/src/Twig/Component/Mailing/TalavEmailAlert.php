<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Mailing;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class EmailAlert.
 */
#[AsTwigComponent]
final class TalavEmailAlert
{
    public string $message;
    public ?string $format = 'html';
}
