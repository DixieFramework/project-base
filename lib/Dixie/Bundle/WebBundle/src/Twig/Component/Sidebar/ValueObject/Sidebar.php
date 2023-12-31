<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Sidebar\ValueObject;

use ArrayIterator;

/**
 * Collection of sidebar items
 * Class SidebarItemCollection.
 */
class Sidebar implements \IteratorAggregate
{
    public function __construct(
        public array $items,
        public array $options = []
    ) {
        $this->items = $this->items['data'];

        $this->items = array_map(function ($item) {
            return match ($item['type']) {
                'header' => Header::fromArray($item),
                'group' => Group::fromArray($item),
                'link' => Link::fromArray($item),
            };
        }, $this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
