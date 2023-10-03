<?php

declare(strict_types=1);

namespace Talav\Component\Resource\Model;

trait ResourceTrait
{
    protected mixed $id;

    public function getId(): mixed
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return empty($this->id);
    }

    /**
     * Return string representation of entity.
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
