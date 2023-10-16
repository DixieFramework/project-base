<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Data;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class MetaDataItem.
 */
#[AsTwigComponent]
final class TalavMetaDataItem
{
    public ?string $label = null;
    public ?string $value = null;
}
