<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Mailing;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class EmailSection.
 */
#[AsTwigComponent]
final class TalavEmailSection
{
    public string $title = '';
    public string $url = '';
    public string $format = 'html';
}
