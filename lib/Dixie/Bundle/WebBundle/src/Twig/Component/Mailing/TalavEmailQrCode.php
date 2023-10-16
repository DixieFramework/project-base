<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Mailing;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class EmailQrCode.
 */
final class TalavEmailQrCode
{
    public string $title = '';
    public string $src = '';
    public string $format = 'html';
}
