<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait IsEnabledTrait
{
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => 1])]
    protected bool $is_enabled;

    public function isIsEnabled(): bool
    {
        return $this->is_enabled;
    }

    public function setIsEnabled(bool $is_enabled): self
    {
        $this->is_enabled = $is_enabled;

        return $this;
    }

    public function getIsEnabledAsText(): string
    {
        return $this->is_enabled ? 'Yes' : 'No';
    }

    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    public function isDisabled(): bool
    {
        return !$this->is_enabled;
    }
}
