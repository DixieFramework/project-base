<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class Note.
 */
#[AsTwigComponent]
final class TalavNote
{
    public string $content;
    public string $author;
    public \DateTimeImmutable $date;
}
