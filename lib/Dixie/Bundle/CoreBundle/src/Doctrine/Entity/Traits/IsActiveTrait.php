<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait IsActiveTrait
{
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    protected bool $is_active;

    public function isIsActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getIsActiveAsText(): string
    {
        return $this->is_active ? 'Yes' : 'No';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isNotActive(): bool
    {
        return !$this->is_active;
    }
}
