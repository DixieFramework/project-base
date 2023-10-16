<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Data;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class MetaDataSection.
 */
#[AsTwigComponent]
final class TalavMetaDataSection
{
    public ?string $title = null;
    public bool $row = false;
}
