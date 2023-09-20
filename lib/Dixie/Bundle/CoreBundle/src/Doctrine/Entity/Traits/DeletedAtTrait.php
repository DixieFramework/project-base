<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Doctrine\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait DeletedAtTrait
{
    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $deleted_at = null;

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->deleted_at = $is_deleted ? new \DateTimeImmutable() : null;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at ? true : false;
    }

    public function getIsDeleted(): bool
    {
        return $this->isDeleted();
    }

    public function getIsDeletedAsText(): string
    {
        return $this->deleted_at ? 'Yes' : 'No';
    }
}
