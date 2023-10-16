<?php

declare(strict_types=1);

namespace Talav\WebBundle\Twig\Component\Table;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Class TransactionTable.
 */
#[AsTwigComponent(template: '@TalavWeb/components/table/transaction.html.twig')]
final class TalavTransactionTable
{
    public mixed $data;
}
