<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Sidebar\ValueObject;

use Webmozart\Assert\Assert;

/**
 * Class SidebarHeader.
 */
class Header
{
    public function __construct(
        public string $label
    ) {
    }

    public static function fromArray(array $data): self
    {
        Assert::keyExists($data, 'label');

        return new self($data['label']);
    }
}
