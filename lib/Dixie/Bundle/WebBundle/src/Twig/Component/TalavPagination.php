<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class DashlitePagination.
 */
#[AsTwigComponent]
final class TalavPagination
{
    public ?SlidingPaginationInterface $data = null;

    public function preMount(?SlidingPaginationInterface $data): void
    {
        $this->data = $data;
    }
}
