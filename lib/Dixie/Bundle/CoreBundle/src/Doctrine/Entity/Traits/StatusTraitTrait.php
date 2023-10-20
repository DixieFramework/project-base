<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait StatusTraitTrait
{
    #[ORM\Column(type: Types::SMALLINT, nullable: false, options: ['default' => 0, 'unsigned' => true])]
    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => 0, 'unsigned' => true])]
    protected int $status;

    static public function getStatusFormChoices(): array
    {
        return array_flip(self::$status_values);
    }

    static public function getStatusValues(): array
    {
        return self::$status_values;
    }

    static public function isStatusExist($status): bool
    {
        if (isset(self::$status_values[$status])) {
            return true;
        }

        return false;
    }

    public function getStatusAsText(): string
    {
        if (isset(self::$status_values[$this->status])) {
            return self::$status_values[$this->status];
        }

        return 'N/A';
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
