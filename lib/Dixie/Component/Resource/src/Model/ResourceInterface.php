<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

interface ResourceInterface
{
    /**
     * Gets resource identifier.
     */
    /** @psalm-suppress MissingReturnType */
    public function getId();
}
