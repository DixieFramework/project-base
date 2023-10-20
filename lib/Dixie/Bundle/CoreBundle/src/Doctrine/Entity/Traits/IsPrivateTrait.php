<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait IsPrivateTrait
{
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 0])]
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    protected bool $is_private = false;

    public function getIsPrivateAsText(): string
    {
        return $this->is_private ? 'Yes' : 'No';
    }

    public function getIsPrivate(): bool
    {
        return $this->is_private;
    }

    public function setIsPrivate(bool $is_private): self
    {
        $this->is_private = $is_private;

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->is_private;
    }

    public function isNotPrivate(): bool
    {
        return !$this->is_private;
    }
}
