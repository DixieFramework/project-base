<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Model;

/**
 * Contains translation query parameters.
 */
class TranslateQuery
{
    public function __construct(
        public string $from = '',
        public string $to = '',
        public string $text = '',
        public ?string $service = null,
        public bool $html = false,
    ) {
    }
}
